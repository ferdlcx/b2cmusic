<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'order_id'   => ['nullable', 'exists:orders,id'],
            'rating'     => ['required', 'integer', 'min:1', 'max:5'],
            'comment'    => ['required', 'string', 'max:1000'],
            'photo'      => ['nullable', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        // If order_id is specified, ensure it belongs to this user
        if ($request->filled('order_id')) {
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->first();
            if (!$order) {
                return back()->with('error', 'Pesanan tidak ditemukan atau tidak valid.');
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reviews', 'public');
        }

        $review = Review::create([
            'product_id' => $request->product_id,
            'user_id'    => $user->id,
            'order_id'   => $request->order_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'photo'      => $photoPath,
            'status'     => 'pending', // Pending moderation by default
        ]);

        try {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'submit_review',
                'model_type' => Review::class,
                'model_id' => $review->id,
                'description' => "Mengirim ulasan untuk produk ID {$request->product_id} dengan rating {$request->rating}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {}

        return back()->with('success', 'Ulasan Anda berhasil dikirim dan sedang menunggu moderasi admin.');
    }
}
