<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return back()->with('success', 'Pembayaran berhasil disimulasikan! Pesanan Anda kini sedang diproses.');
    }
}
