<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\PaymentSuccess;

class OrderController extends Controller
{
    public function history()
    {
        $user = Auth::user();
        $orders = Order::with(['payment', 'shipment'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.history', compact('orders'));
    }

    public function show($orderCode)
    {
        $user = Auth::user();
        $order = Order::with([
            'address', 
            'items.product.primaryImage', 
            'payment', 
            'shipment', 
            'coupons'
        ])
        ->where('user_id', $user->id)
        ->where('order_code', $orderCode)
        ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    public function checkStatus($id)
    {
        $user = Auth::user();
        $order = Order::with('payment')->where('user_id', $user->id)->findOrFail($id);

        if ($order->status !== 'pending' || !$order->payment || !$order->payment->snap_token) {
            return back()->with('error', 'Status pesanan ini tidak dapat dicek ke Midtrans.');
        }

        $serverKey = config('services.midtrans.server_key');
        if (!$serverKey) {
            return back()->with('error', 'Konfigurasi Midtrans belum lengkap.');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
                ->get("https://api.sandbox.midtrans.com/v2/{$order->order_code}/status");

            if ($response->successful()) {
                $statusData = $response->json();
                $transactionStatus = $statusData['transaction_status'] ?? null;
                $fraudStatus = $statusData['fraud_status'] ?? null;

                if ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {
                    DB::beginTransaction();
                    $order->update(['status' => 'paid']);
                    $order->payment->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'transaction_id' => $statusData['transaction_id'] ?? $order->payment->transaction_id,
                    ]);
                    $user->notify((new PaymentSuccess($order))->delay(now()->addMinutes(5)));
                    DB::commit();
                    
                    // Trigger Biteship API
                    $this->processBiteshipOrder($order);
                    
                    try {
                        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\InvoiceMail($order));
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim InvoiceMail: ' . $e->getMessage());
                    }

                    return back()->with('success', 'Pembayaran berhasil dikonfirmasi dari Midtrans! Pesanan Anda kini sedang diproses.');
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    DB::beginTransaction();
                    $order->update(['status' => 'canceled']);
                    $order->payment->update(['status' => 'failed']);
                    // Restore stock
                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                    DB::commit();
                    return back()->with('error', 'Pesanan dibatalkan/kadaluwarsa sesuai status Midtrans.');
                }

                return back()->with('success', 'Status pesanan saat ini di Midtrans: ' . strtoupper($transactionStatus) . '. (Belum Lunas)');
            } else {
                return back()->with('error', 'Gagal mengecek status ke Midtrans. Pastikan pesanan sudah pernah dibayar di simulator.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan jaringan saat mengecek status: ' . $e->getMessage());
        }
    }

    public function handleWebhook(Request $request)
    {
        Log::info('Midtrans Webhook Received:', $request->all());

        $serverKey = config('services.midtrans.server_key');
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');

        // Validate Signature
        $computedSignature = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        if ($computedSignature !== $signatureKey) {
            Log::warning('Midtrans Webhook Invalid Signature:', [
                'received' => $signatureKey,
                'computed' => $computedSignature
            ]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // Find Order
        $order = Order::with(['payment', 'items'])->where('order_code', $orderId)->first();

        if (!$order) {
            Log::warning('Midtrans Webhook: Order not found: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Map Midtrans Status
        $transactionStatus = $request->input('transaction_status');
        $paymentType = $request->input('payment_type');
        $fraudStatus = $request->input('fraud_status');
        $transactionId = $request->input('transaction_id');

        $orderStatus = 'pending';
        $paymentStatus = 'pending';
        $paidAt = null;

        if ($transactionStatus == 'capture') {
            if ($paymentType == 'credit_card') {
                if ($fraudStatus == 'challenge') {
                    $orderStatus = 'pending';
                    $paymentStatus = 'pending';
                } else {
                    $orderStatus = 'paid';
                    $paymentStatus = 'paid';
                    $paidAt = now();
                }
            }
        } elseif ($transactionStatus == 'settlement') {
            $orderStatus = 'paid';
            $paymentStatus = 'paid';
            $paidAt = now();
        } elseif ($transactionStatus == 'pending') {
            $orderStatus = 'pending';
            $paymentStatus = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $orderStatus = 'canceled';
            $paymentStatus = 'failed';
        }

        // Update Status in Database Transaction (with stock restoration)
        DB::beginTransaction();
        try {
            // Restore stock if transitioning to canceled/failed from pending
            if ($orderStatus === 'canceled' && $order->status === 'pending') {
                Log::info("Restoring stock for order {$order->order_code} due to cancellation/expiration.");
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            // Update Order
            $order->update(['status' => $orderStatus]);

            if ($orderStatus === 'paid') {
                $order->user->notify((new PaymentSuccess($order))->delay(now()->addMinutes(5)));
                
                try {
                    \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\InvoiceMail($order));
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim InvoiceMail webhook: ' . $e->getMessage());
                }
            }

            // Update Payment
            if ($order->payment) {
                $order->payment->update([
                    'status' => $paymentStatus,
                    'transaction_id' => $transactionId ?: $order->payment->transaction_id,
                    'paid_at' => $paidAt ?: $order->payment->paid_at,
                ]);
            }

            DB::commit();
            Log::info("Order {$order->order_code} updated successfully via Webhook to: {$orderStatus}");
            
            if ($orderStatus === 'paid') {
                $this->processBiteshipOrder($order);
            }
            
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => $order->user_id,
                    'action' => 'payment_webhook_' . $orderStatus,
                    'model_type' => Order::class,
                    'model_id' => $order->id,
                    'description' => "Status pembayaran pesanan {$order->order_code} diperbarui via Midtrans Webhook menjadi: {$orderStatus}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $e) {}

            return response()->json(['message' => 'Success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing webhook transaction: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function cancel($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->where('id', $id)->firstOrFail();

        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan dengan status Pending yang bisa dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }

            // Cancel order
            $order->update(['status' => 'canceled']);

            // Cancel payment
            if ($order->payment) {
                $order->payment->update(['status' => 'canceled']);
            }

            // Cancel shipment
            if ($order->shipment) {
                $order->shipment->update(['status' => 'canceled']);
            }

            // Log activity
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'cancel_order',
                    'model_type' => Order::class,
                    'model_id' => $order->id,
                    'description' => "Membatalkan pesanan: {$order->order_code}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $e) {}

            DB::commit();
            return redirect()->route('orders.history')->with('success', "Pesanan #{$order->order_code} berhasil dibatalkan. Stok produk telah dikembalikan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function processBiteshipOrder($order)
    {
        if ($order->biteship_order_id) return; // already processed

        $apiKey = env('BITESHIP_API_KEY');
        if (!$apiKey) return;

        $address = \App\Models\Address::find($order->shipping_address_id);
        if (!$address) return;

        $items = [];
        $totalWeight = 0;
        foreach ($order->items as $item) {
            $product = $item->product;
            $weight = $product ? ($product->weight ?: 1000) : 1000;
            $totalWeight += $weight * $item->quantity;
            $items[] = [
                'name' => mb_substr($product ? $product->name : 'Item', 0, 40),
                'value' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'weight' => (int) $weight
            ];
        }

        $courierCompany = strtolower($order->courier_company ?: 'jne');
        $courierType = strtolower($order->courier_type ?: 'reg');

        $payload = [
            'shipper_contact_name' => env('MAIL_FROM_NAME', 'DjudasMS Official'),
            'shipper_contact_phone' => '08123456789',
            'shipper_organization' => 'DjudasMS',
            'origin_contact_name' => 'Admin DjudasMS',
            'origin_contact_phone' => '08123456789',
            'origin_address' => 'Toko DjudasMS',
            'origin_postal_code' => (int) env('BITESHIP_ORIGIN_POSTAL_CODE', 17464),
            'destination_contact_name' => mb_substr($address->name, 0, 50),
            'destination_contact_phone' => mb_substr($address->phone, 0, 20),
            'destination_address' => mb_substr($address->address, 0, 200),
            'destination_postal_code' => (int) $address->postal_code,
            'courier_company' => $courierCompany,
            'courier_type' => $courierType,
            'delivery_type' => 'now',
            'items' => $items
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.biteship.com/v1/orders', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $order->update([
                    'biteship_order_id' => $data['id'] ?? null,
                    'waybill_id' => $data['courier']['waybill_id'] ?? null,
                    'tracking_id' => $data['courier']['tracking_id'] ?? null,
                ]);

                if ($order->shipment) {
                    $order->shipment->update([
                        'tracking_number' => $data['courier']['waybill_id'] ?? null,
                        'status' => 'processing'
                    ]);
                }
            } else {
                Log::error('Biteship create order failed', ['res' => $response->json(), 'payload' => $payload]);
            }
        } catch (\Exception $e) {
            Log::error('Biteship exception: ' . $e->getMessage());
        }
    }

    public function getBiteshipTracking($id)
    {
        $order = Order::findOrFail($id);
        if (!$order->biteship_order_id) {
            return response()->json(['success' => false, 'message' => 'No tracking available']);
        }

        $apiKey = env('BITESHIP_API_KEY');
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'Authorization' => $apiKey
            ])->get("https://api.biteship.com/v1/orders/{$order->biteship_order_id}");

            if ($response->successful()) {
                return response()->json(['success' => true, 'data' => $response->json()]);
            }
            return response()->json(['success' => false, 'message' => 'Failed to fetch tracking']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
