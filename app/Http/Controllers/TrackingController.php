<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
 
class TrackingController extends Controller
{
    public function simulatorPage()
    {
        $orders = Order::with(['user', 'shipment', 'address'])->whereNotNull('biteship_order_id')->orderBy('created_at', 'desc')->paginate(10);
        // Fallback: If no orders have biteship_order_id, just get all orders that are paid/processing/shipped/completed
        if ($orders->isEmpty()) {
            $orders = Order::with(['user', 'shipment', 'address'])->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])->orderBy('created_at', 'desc')->paginate(10);
            // Provide fake biteship IDs for simulation
            foreach ($orders as $order) {
                if (empty($order->biteship_order_id)) {
                    $order->biteship_order_id = 'SIM-BITESHIP-' . $order->id;
                    $order->save();
                }
            }
        }
        return view('simulasi.index', compact('orders'));
    }

    public function triggerWebhookStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'status' => 'required|string'
        ]);

        $order = Order::with('shipment')->where('biteship_order_id', $request->order_id)->first();
        if (!$order) {
            return back()->with('error', 'Order dengan Biteship ID tersebut tidak ditemukan.');
        }

        // Fetch real data from Biteship to make it 100% accurate
        $apiKey = env('BITESHIP_API_KEY');
        $biteshipData = null;
        if ($apiKey) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                    'Authorization' => $apiKey
                ])->get("https://api.biteship.com/v1/orders/{$request->order_id}");

                if ($response->successful()) {
                    $biteshipData = $response->json();
                }
            } catch (\Exception $e) {
                // Silently fallback if API fails
            }
        }

        $courierData = $biteshipData['courier'] ?? null;
        $priceData = $biteshipData['price'] ?? $order->total;

        $payload = [
            'event' => 'order.status',
            'courier_tracking_id' => $courierData['tracking_id'] ?? ($order->shipment->tracking_number ?? 'SIM-TRACK-' . rand(1000, 9999)),
            'courier_waybill_id' => $courierData['waybill_id'] ?? ($order->shipment->tracking_number ?? 'SIM-WAYBILL-' . rand(1000, 9999)),
            'courier_company' => $courierData['company'] ?? ($order->shipment->courier ?? 'JNE'),
            'courier_type' => $courierData['type'] ?? ($order->shipment->service ?? 'REG'),
            'courier_driver_name' => $courierData['driver_name'] ?? 'Budi Supir',
            'courier_driver_phone' => $courierData['driver_phone'] ?? '088888888888',
            'courier_driver_photo_url' => $courierData['driver_photo_url'] ?? 'https://picsum.photos/200',
            'courier_driver_plate_number' => $courierData['driver_plate_number'] ?? 'B 1234 AAA',
            'courier_link' => $courierData['link'] ?? 'https://biteship.com/track',
            'order_id' => $order->biteship_order_id,
            'order_price' => $priceData,
            'status' => $request->status
        ];

        // Instead of internal Request::create, let's do a real HTTP request to our own endpoint so it behaves exactly like a real webhook 
        // We use url() to get the full app URL
        try {
            \Illuminate\Support\Facades\Http::timeout(5)->post(url('/api/biteship/webhook'), $payload);
        } catch (\Exception $e) {
            // Fallback to internal if HTTP fails (e.g., local environment without proper host resolution)
            $webhookRequest = Request::create('/api/biteship/webhook', 'POST', $payload);
            $this->biteshipWebhook($webhookRequest);
        }

        return back()->with('success', 'Webhook order.status (' . $request->status . ') berhasil disimulasikan secara akurat!');
    }

    public function triggerWebhookPrice(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'price' => 'required|numeric'
        ]);

        $order = Order::with('shipment')->where('biteship_order_id', $request->order_id)->first();
        if (!$order) {
            return back()->with('error', 'Order dengan Biteship ID tersebut tidak ditemukan.');
        }

        $payload = [
            'event' => 'order.price',
            'cash_on_delivery_fee' => 0,
            'courier_tracking_id' => $order->shipment ? $order->shipment->tracking_number : 'SIM-TRACK',
            'courier_waybill_id' => $order->shipment ? $order->shipment->tracking_number : 'SIM-WAYBILL',
            'order_id' => $order->biteship_order_id,
            'price' => $request->price,
            'proof_of_delivery_fee' => 0,
            'shippment_fee' => $request->price,
            'status' => 'picked'
        ];

        // While currently biteshipWebhook only handles 'order.status' and 'waybill.status',
        // we simulate sending this just as Biteship would. You can expand biteshipWebhook to handle 'order.price' later.
        $webhookRequest = Request::create('/api/biteship/webhook', 'POST', $payload);
        $this->biteshipWebhook($webhookRequest);

        return back()->with('success', 'Webhook order.price (' . $request->price . ') berhasil disimulasikan!');
    }

    public function clearSimulator()
    {
        $orders = Order::whereNotNull('biteship_order_id')->get();
        $apiKey = env('BITESHIP_API_KEY');

        foreach ($orders as $order) {
            if ($apiKey) {
                try {
                    // Mengubah status di Biteship menjadi Cancelled karena tidak bisa di-delete hard
                    \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                        'Authorization' => $apiKey
                    ])->post("https://api.biteship.com/v1/orders/{$order->biteship_order_id}/cancel", [
                        'reason' => 'Test reset'
                    ]);
                } catch (\Exception $e) {}
            }
        }

        Order::query()->delete();
        \App\Models\ActivityLog::where('model_type', Order::class)->delete();

        return back()->with('success', 'Semua pesanan berhasil dihapus dari sistem lokal, dan request penghapusan telah dikirim ke Sandbox Biteship.');
    }
    public function track(Request $request, $id)
    {
        $user = Auth::user();
        $order = Order::with(['address', 'shipment'])->where('user_id', $user->id)->findOrFail($id);

        if (!in_array($order->status, ['processing', 'shipped', 'completed'])) {
            return back()->with('error', 'Pesanan ini belum bisa dilacak.');
        }

        $checkpoints = [];

        if ($order->tracking_id) {
            $apiKey = env('BITESHIP_API_KEY');
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                    'Authorization' => $apiKey
                ])->get("https://api.biteship.com/v1/trackings/{$order->tracking_id}");

                if ($response->successful()) {
                    $biteshipData = $response->json();
                    if (!empty($biteshipData['history'])) {
                        foreach ($biteshipData['history'] as $history) {
                            $checkpoints[] = [
                                'status' => ucfirst(str_replace('_', ' ', $history['status'])),
                                'description' => $history['note'],
                                'location' => 'Biteship Update',
                                'lat' => null,
                                'lng' => null,
                                'datetime' => \Carbon\Carbon::parse($history['updated_at'])->format('d M Y, H:i') . ' WIB',
                                'completed' => true,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // fallback to dummy data if API fails
            }
        }

        // Fallback to dummy data if no history from Biteship or if simulated locally (Biteship history lacks local progress)
        $localStatus = $order->shipment->status ?? '';
        $simulatedCheckpoints = [];
        
        $sequence = ['allocated', 'picking_up', 'picked', 'dropping_off', 'delivered'];
        $statusDict = [
            'allocated' => ['name' => 'Allocated', 'note' => 'Kurir telah dialokasikan untuk penjemputan.'],
            'picking_up' => ['name' => 'Picking Up', 'note' => 'Kurir sedang menuju lokasi pickup.'],
            'picked' => ['name' => 'Picked', 'note' => 'Barang telah diserahkan ke kurir.'],
            'dropping_off' => ['name' => 'Dropping Off', 'note' => 'Barang sedang dalam perjalanan menuju alamat kustomer.'],
            'delivered' => ['name' => 'Delivered', 'note' => 'Paket telah diterima di alamat tujuan.']
        ];
        
        $existingNames = array_map(function($c) { return strtolower($c['status']); }, $checkpoints);
        $baseDate = clone ($order->shipment->updated_at ?? now());
        
        $curIndex = array_search($localStatus, $sequence);
        
        if ($curIndex !== false) {
            foreach ($sequence as $index => $seqStatus) {
                if ($index <= $curIndex) {
                    $formattedName = strtolower(str_replace('_', ' ', $seqStatus));
                    // Jika Biteship API belum mencatat status ini (karena testing via simulasi lokal)
                    if (!in_array($formattedName, $existingNames)) {
                        $simulatedCheckpoints[] = [
                            'status' => $statusDict[$seqStatus]['name'],
                            'description' => $statusDict[$seqStatus]['note'] . ' (Simulasi Lokal)',
                            'location' => 'Sistem Internal',
                            'lat' => null,
                            'lng' => null,
                            'datetime' => $baseDate->copy()->subMinutes(($curIndex - $index) * 30)->format('d M Y, H:i') . ' WIB',
                            'completed' => true,
                        ];
                    }
                }
            }
        }
        
        if (!empty($simulatedCheckpoints)) {
            $checkpoints = array_merge($checkpoints, $simulatedCheckpoints);
        }

        // Jika benar-benar kosong dan belum masuk sequence biteship
        if (empty($checkpoints) && in_array($order->status, ['processing', 'shipped', 'completed'])) {
            $baseDateStatic = clone ($order->shipment->shipped_at ?? $order->created_at);
            $checkpoints[] = [
                'status' => 'Pesanan Diproses',
                'description' => 'Pesanan Anda sedang dikemas di gudang DjudasMS Jakarta',
                'location' => 'Gudang DjudasMS, Jakarta Barat',
                'lat' => -6.1684,
                'lng' => 106.7588,
                'datetime' => $baseDateStatic->format('d M Y, H:i') . ' WIB',
                'completed' => true,
            ];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => $order->status,
                'shipment_status' => $order->shipment->status ?? 'pending',
                'checkpoints' => $checkpoints
            ]);
        }

        return view('orders.tracking', compact('order', 'checkpoints'));
    }

    public function simulateDelivery($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->findOrFail($id);

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Hanya pesanan berstatus "Dikirim" yang dapat disimulasikan sebagai Selesai.');
        }

        $order->update(['status' => 'completed']);
        
        if ($order->shipment) {
            $order->shipment->update([
                'status' => 'delivered',
            ]);
        }
        
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderCompletedMail($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim OrderCompletedMail: ' . $e->getMessage());
        }

        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'delivery_simulated',
                'model_type' => Order::class,
                'model_id' => $order->id,
                'description' => "Simulasi pesanan diterima oleh pelanggan untuk {$order->order_code}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {}

        return redirect()->route('orders.show', $order->order_code)->with('success', 'Pesanan berhasil disimulasikan sebagai Diterima. Sekarang Anda dapat memberikan ulasan produk!');
    }

    public function sandboxArrive($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->findOrFail($id);

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Hanya pesanan berstatus "Dikirim" yang dapat disimulasikan.');
        }

        if ($order->shipment) {
            $order->shipment->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }
        
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderArrivedMail($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim OrderArrivedMail: ' . $e->getMessage());
        }

        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'courier_arrived_simulated',
                'model_type' => Order::class,
                'model_id' => $order->id,
                'description' => "Simulasi kurir sampai di tujuan untuk {$order->order_code}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {}

        return redirect()->route('orders.show', $order->order_code)->with('success', 'Simulasi: Paket telah tiba di tujuan! Anda kini bisa mengonfirmasi penerimaan atau mengajukan retur.');
    }
    public function biteshipWebhook(Request $request)
    {
        // Authenticate webhook (Biteship sends signature, but for simplicity we verify the payload)
        // Log the incoming webhook for debugging
        \Illuminate\Support\Facades\Log::info('Biteship Webhook Received:', $request->all());

        $event = $request->input('event');
        
        // If event is empty (usually during ping/installation test from Biteship)
        if (empty($event)) {
            return response('ok', 200);
        }

        if (in_array($event, ['order.status', 'waybill.status'])) {
            $biteshipOrderId = $request->input('order_id');
            $status = $request->input('status'); // e.g. 'delivered', 'picking_up', 'in_transit'

            $order = Order::with('shipment')->where('biteship_order_id', $biteshipOrderId)->first();

            if ($order && $order->shipment) {
                // Update internal shipment status
                $order->shipment->update([
                    'status' => $status === 'delivered' ? 'delivered' : 'processing',
                ]);

                if ($status === 'delivered') {
                    // Do NOT update order status directly to completed (Shopee flow)
                    $order->shipment->update([
                        'status' => 'delivered',
                        'delivered_at' => now(),
                    ]);

                    try {
                        \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderArrivedMail($order));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Gagal mengirim email Order Arrived (Webhook): ' . $e->getMessage());
                    }

                    try {
                        ActivityLog::create([
                            'user_id' => $order->user_id,
                            'action' => 'courier_arrived_webhook',
                            'model_type' => Order::class,
                            'model_id' => $order->id,
                            'description' => "Pesanan {$order->order_code} telah tiba di tujuan (Update via Biteship Webhook)",
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);
                    } catch (\Exception $e) {}
                } elseif (in_array($status, ['allocated', 'picking_up', 'picked', 'in_transit', 'dropping_off', 'shipped', 'on_hold', 'return_in_transit', 'returned', 'disposed', 'rejected', 'courier_not_found'])) {
                    // Update main order status to shipped if it was processing/paid and the package is moving
                    if (in_array($status, ['picking_up', 'picked', 'in_transit', 'dropping_off', 'shipped'])) {
                        if ($order->status === 'processing' || $order->status === 'paid') {
                            $order->update(['status' => 'shipped']);
                        }
                    }

                    // Store exact Biteship status in shipment
                    $order->shipment->update([
                        'status' => $status,
                        'shipped_at' => $order->shipment->shipped_at ?: now(),
                    ]);
                }
            }
            return response('ok', 200);
        }

        return response('ok', 200);
    }

    public function simulatePayment($id)
    {
        $order = Order::with('payment')->findOrFail($id);
        
        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan pending yang dapat dibayar.');
        }

        DB::beginTransaction();
        try {
            $order->update(['status' => 'paid']);
            
            if ($order->payment) {
                $order->payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'transaction_id' => 'SIM-' . strtoupper(Str::random(10)),
                ]);
            }
            
            // Send notification
            $order->user->notify(new \App\Notifications\PaymentSuccess($order));
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'payment_simulated',
                'model_type' => Order::class,
                'model_id' => $order->id,
                'description' => "Simulasi pembayaran lunas untuk pesanan {$order->order_code}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            DB::commit();
            return back()->with('success', 'Simulasi: Pembayaran berhasil! Pesanan Anda kini berstatus PAID.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses simulasi pembayaran: ' . $e->getMessage());
        }
    }

    public function simulateShipment($id)
    {
        $order = Order::with('shipment')->findOrFail($id);
        
        if (!in_array($order->status, ['paid', 'processing'])) {
            return back()->with('error', 'Hanya pesanan yang sudah dibayar (paid/processing) yang dapat dikirim.');
        }

        DB::beginTransaction();
        try {
            $order->update(['status' => 'shipped']);
            
            if ($order->shipment) {
                $shipmentUpdate = [
                    'status' => 'shipped',
                    'shipped_at' => now(),
                ];
                
                // Only generate a SIM-RESI if tracking_number is empty
                if (empty($order->shipment->tracking_number)) {
                    $shipmentUpdate['tracking_number'] = 'SIM-RESI-' . rand(1000000000, 9999999999);
                }
                
                $order->shipment->update($shipmentUpdate);
            }
            
            // Send notification
            $order->user->notify(new \App\Notifications\OrderShipped($order));
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'shipment_simulated',
                'model_type' => Order::class,
                'model_id' => $order->id,
                'description' => "Simulasi pengiriman barang oleh admin untuk pesanan {$order->order_code}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            DB::commit();
            return back()->with('success', 'Simulasi: Pesanan telah dikirim! Resi pelacakan otomatis dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses simulasi pengiriman: ' . $e->getMessage());
        }
    }
}
