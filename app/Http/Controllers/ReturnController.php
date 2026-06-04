<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReturnController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $returns = ReturnRequest::with('order')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.returns.index', compact('returns'));
    }

    public function create($orderId)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->findOrFail($orderId);

        // Check if a return request already exists for this order
        $exists = ReturnRequest::where('order_id', $orderId)->exists();
        if ($exists) {
            return redirect()->route('orders.history')->with('error', 'Pengajuan pengembalian barang untuk pesanan ini sudah ada.');
        }

        // Only allow returns if the order is completed (or paid/shipped)
        // Adjust status check based on business flow, e.g., 'completed' or 'paid'
        if ($order->status === 'canceled' || $order->status === 'pending') {
            return redirect()->route('orders.history')->with('error', 'Pesanan yang belum selesai atau dibatalkan tidak dapat dikembalikan.');
        }

        return view('customer.returns.create', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'reason'   => ['required', 'string', 'max:1000'],
            'photo'    => ['nullable', 'image', 'max:2048'],
        ]);

        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->findOrFail($request->order_id);

        // Check if return request already exists
        $exists = ReturnRequest::where('order_id', $order->id)->exists();
        if ($exists) {
            return redirect()->route('returns.index')->with('error', 'Pengajuan pengembalian barang untuk pesanan ini sudah ada.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('returns', 'public');
        }

        $returnReq = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id'  => $user->id,
            'reason'   => $request->reason,
            'photo'    => $photoPath,
            'status'   => 'pending',
        ]);

        try {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'submit_return',
                'model_type' => ReturnRequest::class,
                'model_id' => $returnReq->id,
                'description' => "Mengajukan pengembalian barang untuk pesanan {$order->order_code}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {}

        return redirect()->route('returns.index')->with('success', 'Pengajuan pengembalian barang Anda berhasil dikirim dan akan segera diproses oleh admin.');
    }
}
