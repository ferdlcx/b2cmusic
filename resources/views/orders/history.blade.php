@extends('layouts.app')

@section('title', 'Riwayat Pesanan - DjudasMS')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-200/60 pb-8 space-y-2">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Daftar Transaksi</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tight text-slate-950">Riwayat Pesanan</h1>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[36px] p-16 text-center text-slate-500 shadow-sm space-y-5">
            <div class="w-16 h-16 bg-slate-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                <i data-lucide="clipboard-list" class="w-8 h-8"></i>
            </div>
            <div class="space-y-1">
                <p class="font-display text-lg font-bold text-slate-900 uppercase">Belum Ada Riwayat Pesanan</p>
                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">Seluruh transaksi pembelian instrumen atau vinyl yang Anda buat akan tercatat di sini.</p>
            </div>
            <div class="pt-2">
                <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 text-white rounded-2xl text-xs font-semibold tracking-wider hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i> Belanja Sekarang
                </a>
            </div>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200/80 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-200/60">
                        <tr>
                            <th class="px-6 py-4.5 font-bold">Kode Pesanan</th>
                            <th class="px-6 py-4.5 font-bold">Tanggal</th>
                            <th class="px-6 py-4.5 font-bold">Total Belanja</th>
                            <th class="px-6 py-4.5 font-bold">Metode Bayar</th>
                            <th class="px-6 py-4.5 font-bold text-center">Status</th>
                            <th class="px-6 py-4.5 font-bold text-center">Aksi / Simulasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/40 transition">
                                <!-- Order Code -->
                                <td class="px-6 py-5 font-bold text-slate-900 uppercase">
                                    <a href="{{ route('orders.show', $order->order_code) }}" class="font-display hover:text-indigo-600 transition font-black">
                                        #{{ $order->order_code }}
                                    </a>
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-5 text-slate-500 font-semibold text-xs">
                                    {{ $order->created_at->format('d/M/Y, H:i') }} WIB
                                </td>

                                <!-- Total -->
                                <td class="px-6 py-5 font-black text-slate-900">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>

                                <!-- Payment Method -->
                                <td class="px-6 py-5 text-slate-500 uppercase font-bold text-xs tracking-wider">
                                    {{ $order->payment->payment_method }}
                                </td>

                                <!-- Order Status -->
                                <td class="px-6 py-5 text-center">
                                    @if($order->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Menunggu Pembayaran
                                        </span>
                                    @elseif($order->status === 'paid')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Dibayar
                                        </span>
                                    @elseif($order->status === 'processing')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                            Diproses
                                        </span>
                                    @elseif($order->status === 'shipped')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Dikirim
                                        </span>
                                    @elseif($order->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-slate-50 text-slate-500 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>

                                <!-- Action & Simulation -->
                                <td class="px-6 py-5 text-center space-x-3">
                                    <a href="{{ route('orders.show', $order->order_code) }}" class="inline-flex items-center gap-1 text-xs uppercase tracking-wider font-bold text-slate-600 hover:text-indigo-600 transition">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                    </a>
                                    
                                    @if($order->status === 'pending')
                                        <a href="{{ route('orders.show', $order->order_code) }}"
                                            class="inline-flex items-center gap-1 text-xs uppercase tracking-widest bg-indigo-600 text-white px-3.5 py-2 rounded-xl hover:bg-indigo-700 hover:shadow-md hover:shadow-indigo-600/15 transition duration-300 font-semibold shadow-sm">
                                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Bayar
                                        </a>
                                    @endif
                                    @if($order->status === 'completed')
                                        <a href="{{ route('returns.create', $order->id) }}" class="text-[0.7rem] text-amber-600 font-bold hover:underline">Retur</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
