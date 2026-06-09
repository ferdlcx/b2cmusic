@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard - DjudasMS')

@section('admin_content')
<div class="space-y-12 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-walnut-800/10">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Halaman Kontrol</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Dashboard Admin.</h1>
            <p class="text-sm text-muted font-medium pt-2">Pantau performa toko, pesanan masuk, dan status operasional dari satu panel.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.products') }}" class="inline-flex items-center px-5 py-2 bg-walnut-900 text-gold-500 text-[0.65rem] font-bold uppercase tracking-widest hover:bg-gold-600 hover:text-white transition duration-300">Kelola Produk</a>
            <a href="{{ route('admin.orders') }}" class="inline-flex items-center px-4 py-2 border border-walnut-800/20 bg-transparent text-[0.65rem] font-bold uppercase tracking-widest text-walnut-900 hover:border-gold-500 hover:text-gold-600 transition">Kelola Pesanan</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Revenue -->
        <div class="bg-cream-50 border border-walnut-800/10 p-6 space-y-4 hover:border-gold-500 transition">
            <span class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Total Pendapatan</span>
            <div class="text-3xl font-display font-black text-walnut-950">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <span class="text-xs text-muted font-medium">Dari transaksi lunas</span>
        </div>

        <!-- Orders -->
        <div class="bg-cream-50 border border-walnut-800/10 p-6 space-y-4 hover:border-gold-500 transition">
            <span class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Jumlah Pesanan</span>
            <div class="text-3xl font-display font-black text-walnut-950">{{ $totalOrders }} Pesanan</div>
            <span class="text-xs text-muted font-medium">Total seluruh transaksi</span>
        </div>

        <!-- Products -->
        <div class="bg-cream-50 border border-walnut-800/10 p-6 space-y-4 hover:border-gold-500 transition">
            <span class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Katalog Produk</span>
            <div class="text-3xl font-display font-black text-walnut-950">{{ $totalProducts }} Item</div>
            <span class="text-xs text-muted font-medium">Aktif di database</span>
        </div>

        <!-- Pending Orders -->
        <div class="bg-cream-50 border border-walnut-800/10 p-6 space-y-4 hover:border-gold-500 transition">
            <span class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Menunggu Bayar</span>
            <div class="text-3xl font-display font-black text-walnut-950">{{ $pendingOrdersCount }} Transaksi</div>
            <span class="text-xs text-muted font-medium">Butuh konfirmasi simulasi</span>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="space-y-6 pt-6">
        <h2 class="text-[0.7rem] font-bold uppercase tracking-widest text-walnut-950 border-b border-walnut-800/10 pb-2">Pesanan Masuk Terbaru</h2>
        
        @if($recentOrders->isEmpty())
            <div class="bg-cream-50 border border-walnut-800/10 p-10 text-center text-muted">
                Belum ada pesanan masuk.
            </div>
        @else
            <div class="overflow-x-auto border border-walnut-800/10 bg-cream-50">
                <table class="w-full text-left">
                    <thead class="text-[0.65rem] uppercase tracking-widest text-muted border-b border-walnut-800/10">
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
                            <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-100 transition">
                                <td class="px-6 py-5 font-display text-[0.8rem] font-black uppercase tracking-tight text-walnut-950">#{{ $order->order_code }}</td>
                                <td class="px-6 py-5 text-walnut-900 font-medium">{{ $order->user->name }}</td>
                                <td class="px-6 py-5 text-muted">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-5 font-bold text-walnut-950">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
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
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-[0.65rem] uppercase tracking-widest font-bold text-gold-600 hover:text-walnut-950 transition">Kelola</a>
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
