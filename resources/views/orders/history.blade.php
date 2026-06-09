@extends('layouts.app')

@section('title', 'Riwayat Pesanan - DjudasMS')

@section('content')
<div class="space-y-12 py-8">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-8 space-y-4">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Daftar Transaksi</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Riwayat <span class="text-gold-500">Pesanan.</span></h1>
    </div>

    @if($orders->isEmpty())
        <div class="py-24 text-center space-y-6 bg-cream-50 border border-walnut-800/10">
            <i data-lucide="clipboard-list" class="w-12 h-12 text-walnut-800/20 mx-auto"></i>
            <div class="space-y-2">
                <p class="font-display text-2xl font-black text-walnut-950 uppercase tracking-tighter">Belum Ada Riwayat Pesanan</p>
                <p class="text-[0.8rem] text-muted max-w-sm mx-auto font-medium">Seluruh transaksi pembelian instrumen atau vinyl yang Anda buat akan tercatat di sini.</p>
            </div>
            <div class="pt-4">
                <a href="{{ route('catalog') }}" class="inline-block px-8 py-4 bg-walnut-900 text-gold-500 text-[0.65rem] uppercase tracking-[0.2em] font-bold hover:bg-gold-600 hover:text-white transition duration-300">
                    Jelajahi Katalog
                </a>
            </div>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-[0.65rem] uppercase tracking-widest text-muted border-b border-walnut-800/20">
                    <tr>
                        <th class="pb-4 font-bold">Kode Pesanan</th>
                        <th class="pb-4 font-bold">Tanggal</th>
                        <th class="pb-4 font-bold">Total Belanja</th>
                        <th class="pb-4 font-bold">Metode Bayar</th>
                        <th class="pb-4 font-bold">Status</th>
                        <th class="pb-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-50 transition">
                            <!-- Order Code -->
                            <td class="py-6 font-display font-black text-[0.9rem] text-walnut-950 uppercase tracking-tight">
                                <a href="{{ route('orders.show', $order->order_code) }}" class="hover:text-gold-600 transition">
                                    #{{ $order->order_code }}
                                </a>
                            </td>

                            <!-- Date -->
                            <td class="py-6 text-muted font-medium text-[0.75rem]">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>

                            <!-- Total -->
                            <td class="py-6 font-bold text-walnut-900 text-[0.75rem] tracking-widest">
                                IDR {{ number_format($order->total, 0, ',', '.') }}
                            </td>

                            <!-- Payment Method -->
                            <td class="py-6 text-muted uppercase font-bold text-[0.65rem] tracking-widest">
                                {{ $order->payment->payment_method }}
                            </td>

                            <!-- Order Status -->
                            <td class="py-6">
                                @if($order->status === 'pending')
                                    <span class="inline-block px-2 py-1 border border-walnut-500 text-[0.6rem] font-bold uppercase tracking-widest text-walnut-500">
                                        Pending
                                    </span>
                                @elseif($order->status === 'paid')
                                    <span class="inline-block px-2 py-1 bg-walnut-900 text-gold-500 text-[0.6rem] font-bold uppercase tracking-widest">
                                        Dibayar
                                    </span>
                                @elseif($order->status === 'processing')
                                    <span class="inline-block px-2 py-1 border border-walnut-900 text-[0.6rem] font-bold uppercase tracking-widest text-walnut-900">
                                        Diproses
                                    </span>
                                @elseif($order->status === 'shipped')
                                    <span class="inline-block px-2 py-1 bg-cream-100 border border-gold-500 text-[0.6rem] font-bold uppercase tracking-widest text-gold-600">
                                        Dikirim
                                    </span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-block px-2 py-1 bg-walnut-900 text-gold-500 text-[0.6rem] font-bold uppercase tracking-widest">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 border border-red-600 text-[0.6rem] font-bold uppercase tracking-widest text-red-600">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>

                            <!-- Action & Simulation -->
                            <td class="py-6 text-right space-x-3">
                                <a href="{{ route('orders.show', $order->order_code) }}" class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-600 hover:text-gold-600 transition">
                                    Detail
                                </a>
                                
                                @if($order->status === 'pending')
                                    <a href="{{ route('orders.show', $order->order_code) }}"
                                        class="inline-block text-[0.65rem] uppercase tracking-widest bg-walnut-900 text-gold-500 px-3 py-1.5 hover:bg-gold-600 hover:text-white transition duration-300 font-bold">
                                        Bayar
                                    </a>
                                @endif
                                @if($order->status === 'completed' && $order->updated_at->addDays(30)->isFuture())
                                    <a href="{{ route('returns.create', $order->id) }}" class="text-[0.65rem] uppercase tracking-widest text-red-600 font-bold hover:text-red-800 transition">Retur</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
