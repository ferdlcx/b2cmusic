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

        // Store Origin (Jakarta Barat)
        $originLat = -6.1683;
        $originLng = 106.7588;

        // Customer Destination
        $destLat = $order->address->latitude ?? null;
        $destLng = $order->address->longitude ?? null;

        // If no precise coords, use center of Indonesia as fallback
        if (!$destLat || !$destLng) {
            $destLat = -0.7893;
            $destLng = 113.9213;
        }

        return view('orders.tracking', compact('order', 'originLat', 'originLng', 'destLat', 'destLng'));
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
}
