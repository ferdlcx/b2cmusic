<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfDay()->toDateString());

        $ordersQuery = Order::with('user')
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $orders = (clone $ordersQuery)->orderBy('created_at', 'desc')->get();

        $totalSales = $orders->sum('total');
        $ordersCount = $orders->count();
        $averageOrderValue = $ordersCount > 0 ? $totalSales / $ordersCount : 0;

        // Sales by day for Chart.js
        $salesByDay = Order::selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(id) as count')
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.reports.sales', compact(
            'orders',
            'totalSales',
            'ordersCount',
            'averageOrderValue',
            'salesByDay',
            'startDate',
            'endDate'
        ));
    }

    public function productReport()
    {
        // Top selling products based on order items
        $topProducts = Product::with(['category', 'primaryImage'])
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->whereIn('orders.status', ['paid', 'processing', 'shipped', 'completed'])
            ->groupBy('products.id')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Low stock products
        $lowStockProducts = Product::with(['category', 'primaryImage'])
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->paginate(10);

        return view('admin.reports.products', compact('topProducts', 'lowStockProducts'));
    }

    public function customerReport()
    {
        // Top customers based on order totals
        $topCustomers = User::select('users.*')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->selectRaw('users.id, users.name, users.email, COUNT(orders.id) as orders_count, SUM(orders.total) as total_spent')
            ->whereIn('orders.status', ['paid', 'processing', 'shipped', 'completed'])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(15)
            ->get();

        return view('admin.reports.customers', compact('topCustomers'));
    }

    public function exportSalesPdf(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfDay()->toDateString());

        $orders = Order::with('user')
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $orders->sum('total');

        $pdf = Pdf::loadView('admin.reports.sales_pdf', compact('orders', 'totalSales', 'startDate', 'endDate'));
        return $pdf->download("laporan-penjualan-{$startDate}-to-{$endDate}.pdf");
    }

    public function exportSalesExcel(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfDay()->toDateString());

        $orders = Order::with('user')
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"laporan-penjualan-{$startDate}-to-{$endDate}.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            // Write BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['No', 'Kode Pesanan', 'Tanggal', 'Nama Pelanggan', 'Subtotal', 'Biaya Kirim', 'Diskon', 'Total']);

            foreach ($orders as $index => $order) {
                fputcsv($file, [
                    $index + 1,
                    $order->order_code,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user->name,
                    $order->subtotal,
                    $order->shipping_cost,
                    $order->discount,
                    $order->total
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
