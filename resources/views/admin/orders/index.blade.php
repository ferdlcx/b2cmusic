@extends('layouts.app')

@section('title', 'Kelola Pesanan - Admin DjudasMS')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-8 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-muted font-bold">Menu Admin</span>
            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-walnut-950 mt-3">Daftar Pesanan</h1>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs uppercase tracking-widest font-bold text-muted hover:text-walnut-950 transition">← Dashboard</a>
    </div>

    <!-- Orders Table -->
    @if($orders->isEmpty())
        <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-16 text-center text-muted">
            Belum ada pesanan masuk dari pembeli.
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-walnut-800/10 bg-cream-50 shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-walnut-400 bg-cream-100 border-b border-walnut-800/10">
                    <tr>
                        <th class="px-6 py-4">Kode Invoice</th>
                        <th class="px-6 py-4">Nama Pelanggan</th>
                        <th class="px-6 py-4">Tanggal Masuk</th>
                        <th class="px-6 py-4">Total Tagihan</th>
                        <th class="px-6 py-4 text-center">Status Pesanan</th>
                        <th class="px-6 py-4 text-center">Status Bayar</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-100/50">
                            <!-- Invoice Code -->
                            <td class="px-6 py-6 font-bold text-walnut-900 uppercase">
                                #{{ $order->order_code }}
                            </td>

                            <!-- Customer Name -->
                            <td class="px-6 py-6 text-walnut-800 font-medium">
                                {{ $order->user->name }}
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-6 text-muted">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>

                            <!-- Total Bill -->
                            <td class="px-6 py-6 font-bold text-walnut-900">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>

                            <!-- Order Status -->
                            <td class="px-6 py-6 text-center">
                                @if($order->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gold-50 text-gold-700 border border-gold-200 uppercase">Pending</span>
                                @elseif($order->status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gold-50 text-gold-700 border border-gold-200 uppercase">Paid</span>
                                @elseif($order->status === 'shipped')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase">Shipped</span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">Completed</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-cream-100 text-muted border border-walnut-800/10 uppercase">Canceled</span>
                                @endif
                            </td>

                            <!-- Payment Status -->
                            <td class="px-6 py-6 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $order->payment && $order->payment->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-gold-50 text-gold-700' }} uppercase">
                                    {{ $order->payment && $order->payment->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                                </span>
                            </td>

                            <!-- Manage Link -->
                            <td class="px-6 py-6 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs uppercase tracking-wider font-bold bg-walnut-950 text-white px-3.5 py-1.5 rounded-xl hover:bg-walnut-800 transition shadow-sm">Kelola</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
