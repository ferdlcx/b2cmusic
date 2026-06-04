<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($orderCode)
    {
        $user = Auth::user();
        
        $query = Order::with([
            'address',
            'items.product',
            'payment',
            'shipment',
            'coupons'
        ])->where('order_code', $orderCode);

        // If the logged in user is not admin, they must own the order
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $order = $query->firstOrFail();

        $pdf = Pdf::loadView('invoices.pdf', compact('order'));
        return $pdf->download('invoice-' . $order->order_code . '.pdf');
    }
}
