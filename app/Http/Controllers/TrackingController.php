<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function track($id)
    {
        $user = Auth::user();
        $order = Order::with(['address', 'shipment'])->where('user_id', $user->id)->findOrFail($id);

        if (!in_array($order->status, ['processing', 'shipped', 'completed'])) {
            return back()->with('error', 'Pesanan ini belum bisa dilacak.');
        }

        $checkpoints = [];

        if ($order->shipment) {
            $checkpoints = $this->generateMockCheckpoints($order);
        }

        // Fallback to dummy data if no history from Biteship or no biteship_order_id
        if (empty($checkpoints)) {
            // Determine destination city from order address
            $destinationCity = $order->address ? $order->address->city : 'Kota Tujuan';

            // Base checkpoints for all orders that have been shipped
            if (in_array($order->status, ['processing', 'shipped', 'completed'])) {
            $baseDate = $order->shipment->shipped_at ?? $order->created_at;

            $checkpoints[] = [
                'status' => 'Pesanan Diproses',
                'description' => 'Pesanan Anda sedang dikemas di gudang DjudasMS Jakarta',
                'location' => 'Gudang DjudasMS, Jakarta Barat',
                'lat' => -6.1684,
                'lng' => 106.7588,
                'datetime' => $baseDate->format('d M Y, H:i') . ' WIB',
                'completed' => true,
            ];

            $checkpoints[] = [
                'status' => 'Diserahkan ke Kurir',
                'description' => 'Paket telah diserahkan ke ' . ($order->shipment->courier ?? 'Kurir'),
                'location' => 'Drop Point ' . ($order->shipment->courier ?? 'Kurir') . ', Jakarta Barat',
                'lat' => -6.1751,
                'lng' => 106.7890,
                'datetime' => $baseDate->copy()->addHours(3)->format('d M Y, H:i') . ' WIB',
                'completed' => true,
            ];
        }

        if (in_array($order->status, ['shipped', 'completed'])) {
            $baseDate = $order->shipment->shipped_at ?? $order->created_at;

            $checkpoints[] = [
                'status' => 'Dalam Perjalanan',
                'description' => 'Paket sedang dalam proses sortir di hub Jakarta',
                'location' => 'Sorting Center Jakarta',
                'lat' => -6.2088,
                'lng' => 106.8456,
                'datetime' => $baseDate->copy()->addHours(8)->format('d M Y, H:i') . ' WIB',
                'completed' => true,
            ];

            $checkpoints[] = [
                'status' => 'Transit Hub',
                'description' => 'Paket sedang dalam transit menuju kota tujuan',
                'location' => 'Hub Distribusi Regional',
                'lat' => -6.3000,
                'lng' => 106.9000,
                'datetime' => $baseDate->copy()->addHours(18)->format('d M Y, H:i') . ' WIB',
                'completed' => true,
            ];
        }

            if ($order->shipment && $order->shipment->status === 'delivered') {
                $checkpoints[] = [
                    'status' => 'Terkirim',
                    'description' => 'Paket telah diterima di alamat tujuan',
                    'location' => $order->address->address ?? $destinationCity,
                    'lat' => $order->address->latitude ?? -6.9210,
                    'lng' => $order->address->longitude ?? 107.6210,
                    'datetime' => ($order->shipment->delivered_at ?? now())->format('d M Y, H:i') . ' WIB',
                    'completed' => true,
                ];
            }
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
}
