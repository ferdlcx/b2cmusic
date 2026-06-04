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

    public function pay($id)
    {
        $user = Auth::user();
        $order = Order::with('payment')->where('user_id', $user->id)->findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan ini tidak dapat dibayar.');
        }

        // Simulate payment completion
        $order->update(['status' => 'paid']);
        $order->payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $user->notify(new PaymentSuccess($order));

        return back()->with('success', 'Pembayaran berhasil disimulasikan! Pesanan Anda kini sedang diproses.');
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
                $order->user->notify(new PaymentSuccess($order));
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
            
            return response()->json(['message' => 'Success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing webhook transaction: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
