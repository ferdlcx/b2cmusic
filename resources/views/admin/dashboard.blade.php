@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard - MusicStore Luxe')

@section('admin_content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-8 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Halaman Kontrol</span>
            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Admin Dashboard</h1>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products') }}" class="text-xs uppercase tracking-widest font-black bg-slate-950 text-white px-5 py-3 rounded-xl hover:bg-slate-800 transition">Kelola Produk</a>
            <a href="{{ route('admin.orders') }}" class="text-xs uppercase tracking-widest font-black border border-slate-300 text-slate-700 px-5 py-3 rounded-xl hover:border-slate-950 hover:text-slate-950 transition">Kelola Pesanan</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Revenue -->
        <div class="bg-white border border-slate-200 rounded-[32px] p-6 shadow-sm space-y-4">
            <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Total Pendapatan</span>
            <div class="text-2xl font-black text-slate-950">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <span class="text-xs text-slate-400 font-medium">Dari transaksi lunas</span>
        </div>

        <!-- Orders -->
        <div class="bg-white border border-slate-200 rounded-[32px] p-6 shadow-sm space-y-4">
            <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Jumlah Pesanan</span>
            <div class="text-2xl font-black text-slate-950">{{ $totalOrders }} Pesanan</div>
            <span class="text-xs text-slate-400 font-medium">Total seluruh transaksi</span>
        </div>

        <!-- Products -->
        <div class="bg-white border border-slate-200 rounded-[32px] p-6 shadow-sm space-y-4">
            <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Katalog Produk</span>
            <div class="text-2xl font-black text-slate-950">{{ $totalProducts }} Item</div>
            <span class="text-xs text-slate-400 font-medium">Aktif di database</span>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white border border-slate-200 rounded-[32px] p-6 shadow-sm space-y-4">
            <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Menunggu Bayar</span>
            <div class="text-2xl font-black text-slate-950">{{ $pendingOrdersCount }} Transaksi</div>
            <span class="text-xs text-slate-400 font-medium">Butuh konfirmasi simulasi</span>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="space-y-6 pt-6">
        <h2 class="text-2xl font-black uppercase tracking-tight text-slate-950">Pesanan Masuk Terbaru</h2>
        
        @if($recentOrders->isEmpty())
            <div class="bg-white border border-slate-200 rounded-[32px] p-10 text-center text-slate-500">
                Belum ada pesanan masuk.
            </div>
        @else
            <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Invoice</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                                <td class="px-6 py-5 font-bold text-slate-900 uppercase">#{{ $order->order_code }}</td>
                                <td class="px-6 py-5 text-slate-700 font-medium">{{ $order->user->name }}</td>
                                <td class="px-6 py-5 text-slate-500">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-5 font-bold text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-5 text-center">
                                    @if($order->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">Pending</span>
                                    @elseif($order->status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">Paid</span>
                                    @elseif($order->status === 'shipped')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase">Shipped</span>
                                    @elseif($order->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">Completed</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-50 text-slate-500 border border-slate-200 uppercase">Canceled</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs uppercase tracking-wider font-bold text-slate-950 hover:underline">Kelola</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
