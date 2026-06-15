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
        $orders = Order::with(['user', 'shipment', 'address', 'items.product'])->whereNotNull('biteship_order_id')->orderBy('created_at', 'desc')->paginate(10);
        // Fallback: If no orders have biteship_order_id, just get all orders that are paid/processing/shipped/completed
        if ($orders->isEmpty()) {
            $orders = Order::with(['user', 'shipment', 'address', 'items.product'])->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])->orderBy('created_at', 'desc')->paginate(10);
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

        $order = Order::with(['shipment', 'address'])->where('biteship_order_id', $request->order_id)->first();
        if (!$order) {
            return back()->with('error', 'Order dengan Biteship ID tersebut tidak ditemukan.');
        }

        if ($request->status === 'completed') {
            $order->update(['status' => 'completed']);
            return back()->with('success', 'Pesanan berhasil disimulasikan sebagai Selesai (Completed)!');
        }

        if ($order->shipment) {
            $originLat = -6.1684;
            $originLng = 106.7588;
            $destLat = doubleval($order->address->latitude ?? ($originLat - 0.05));
            $destLng = doubleval($order->address->longitude ?? ($originLng + 0.05));
            
            $pct = match(strtolower($request->status)) {
                'confirmed' => 0.0,
                'allocated' => 0.0,
                'pickingup', 'picking_up' => 0.0,
                'picked' => 0.0,
                'droppingoff', 'dropping_off' => 0.5,
                'delivered' => 1.0,
                'returnintransit', 'return_in_transit' => 0.5,
                'returned' => 0.0,
                default => 0.5,
            };

            $lat = $originLat + ($destLat - $originLat) * $pct;
            $lng = $originLng + ($destLng - $originLng) * $pct;

            $order->shipment->appendStatus(
                $request->status,
                'Update status ' . $request->status,
                'Sistem Tracking (Simulasi)',
                'simulation',
                $lat,
                $lng
            );
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
        
        // Use status_history directly
        if ($order->shipment && !empty($order->shipment->status_history)) {
            $history = $order->shipment->status_history;
            foreach ($history as $index => $item) {
                // Ensure coordinates fallback to address if missing
                if (empty($item['lat']) || empty($item['lng'])) {
                    $item['lat'] = doubleval($order->address->latitude ?? -6.1684);
                    $item['lng'] = doubleval($order->address->longitude ?? 106.7588);
                }
                
                $checkpoints[] = [
                    'status' => ucfirst(str_replace('_', ' ', $item['status'])),
                    'description' => $item['description'] . (isset($item['source']) && $item['source'] === 'simulation' ? ' [TEST MODE]' : ''),
                    'location' => $item['location'],
                    'lat' => $item['lat'],
                    'lng' => $item['lng'],
                    'datetime' => \Carbon\Carbon::parse($item['datetime'])->format('d M Y, H:i') . ' WIB',
                    'completed' => true,
                    'source' => $item['source'] ?? 'system'
                ];
            }
        } else if ($order->shipment) {
            // Backward compatibility / Fallback if history is empty but shipment exists
            $order->shipment->appendStatus('confirmed', 'Pesanan telah terkonfirmasi dan sedang diproses.', 'Gudang DjudasMS, Jakarta Barat', 'system', -6.1684, 106.7588);
            return $this->track($request, $id); // Recursively call again now that history exists
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
            $originLat = -6.1684;
            $originLng = 106.7588;
            $destLat = doubleval($order->address->latitude ?? ($originLat - 0.05));
            $destLng = doubleval($order->address->longitude ?? ($originLng + 0.05));
            
            $order->shipment->appendStatus('delivered', 'Paket telah dikonfirmasi diterima oleh kustomer.', 'Alamat Penerima', 'simulation', $destLat, $destLng);
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
            $originLat = -6.1684;
            $originLng = 106.7588;
            $destLat = doubleval($order->address->latitude ?? ($originLat - 0.05));
            $destLng = doubleval($order->address->longitude ?? ($originLng + 0.05));
            
            $order->shipment->appendStatus('delivered', 'Kurir telah tiba dan paket diserahkan.', 'Alamat Penerima', 'simulation', $destLat, $destLng);
            $order->shipment->update(['delivered_at' => now()]);
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
        // Authenticate webhook
        \Illuminate\Support\Facades\Log::info('Biteship Webhook Received:', $request->all());

        $event = $request->input('event');
        
        if (empty($event)) {
            return response('ok', 200);
        }

        if (in_array($event, ['order.status', 'waybill.status'])) {
            $biteshipOrderId = $request->input('order_id');
            $status = $request->input('status'); // e.g. 'delivered', 'pickingUp'

            $order = Order::with('shipment')->where('biteship_order_id', $biteshipOrderId)->first();

            if ($order && $order->shipment) {
                // Determine if this is Simulation or Real Biteship Webhook
                $isSimulation = $request->header('X-Simulation') === 'true' 
                    || $request->input('X-Simulation') === true 
                    || $request->input('x_simulation') === true;

                $order->update(['is_simulation' => $isSimulation]);

                if ($status === 'delivered') {
                    $order->shipment->appendStatus('delivered', 'Paket telah diterima di alamat tujuan', 'Alamat Penerima', $isSimulation ? 'simulation' : 'biteship', null, null);
                    $order->shipment->update(['delivered_at' => now()]);

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
                } elseif ($status === 'returned') {
                    $order->shipment->appendStatus('returned', 'Barang telah dikembalikan ke Gudang', 'Gudang DjudasMS', $isSimulation ? 'simulation' : 'biteship', -6.1684, 106.7588);
                } else {
                    // Update main order status to shipped if it was processing/paid and the package is moving
                    $activeShippingStatuses = ['pickingUp', 'picking_up', 'picked', 'droppingOff', 'dropping_off', 'in_transit', 'shipped', 'returnInTransit', 'return_in_transit'];
                    if (in_array($status, $activeShippingStatuses)) {
                        if (in_array($order->status, ['processing', 'paid'])) {
                            $order->update(['status' => 'shipped']);
                            $order->shipment->update(['shipped_at' => $order->shipment->shipped_at ?: now()]);
                        }
                    }

                    // Append exact Biteship status
                    $order->shipment->appendStatus($status, 'Update status pengiriman dari kurir', 'Sistem Biteship', $isSimulation ? 'simulation' : 'biteship', null, null);
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
            $order->update(['status' => 'processing']);
            
            if ($order->payment) {
                $order->payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'transaction_id' => 'SIM-' . strtoupper(Str::random(10)),
                ]);
            }
            
            if ($order->shipment) {
                $order->shipment->appendStatus('confirmed', 'Pesanan telah terkonfirmasi dan sedang diproses.', 'Gudang DjudasMS, Jakarta Barat', 'simulation', -6.1684, 106.7588);
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
                    'shipped_at' => now(),
                ];
                
                // Only generate a SIM-RESI if tracking_number is empty
                if (empty($order->shipment->tracking_number)) {
                    $shipmentUpdate['tracking_number'] = 'SIM-RESI-' . rand(1000000000, 9999999999);
                }
                
                $order->shipment->update($shipmentUpdate);
                $order->shipment->appendStatus('shipped', 'Pesanan telah diserahkan ke kurir.', 'Gudang DjudasMS, Jakarta Barat', 'simulation', -6.1684, 106.7588);
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
