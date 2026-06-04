<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Transaction history
        $orders = Order::with(['payment', 'shipment'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // 2. Orders summary
        $totalOrdersCount = Order::where('user_id', $user->id)->count();
        $pendingOrdersCount = Order::where('user_id', $user->id)->where('status', 'pending')->count();
        $paidOrdersCount = Order::where('user_id', $user->id)->where('status', 'paid')->count();
        $processingOrdersCount = Order::where('user_id', $user->id)->where('status', 'processing')->count();
        $shippedOrdersCount = Order::where('user_id', $user->id)->where('status', 'shipped')->count();
        $completedOrdersCount = Order::where('user_id', $user->id)->where('status', 'completed')->count();
        $canceledOrdersCount = Order::where('user_id', $user->id)->where('status', 'canceled')->count();

        // 3. Last order status
        $lastOrder = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

        // 4. Wishlist items count
        $wishlist = Wishlist::where('user_id', $user->id)->first();
        $wishlistCount = $wishlist ? $wishlist->items()->count() : 0;

        // 5. Saved addresses count
        $addressesCount = $user->addresses()->count();

        // 6. Active coupons
        $now = now();
        $activeCoupons = Coupon::where('status', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->get();

        return view('customer.dashboard', compact(
            'orders',
            'totalOrdersCount',
            'pendingOrdersCount',
            'paidOrdersCount',
            'processingOrdersCount',
            'shippedOrdersCount',
            'completedOrdersCount',
            'canceledOrdersCount',
            'lastOrder',
            'wishlistCount',
            'addressesCount',
            'activeCoupons'
        ));
    }
}
