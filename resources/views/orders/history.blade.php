@extends('layouts.app')

@section('title', 'Riwayat Pesanan - MusicStore Luxe')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-8 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Daftar Transaksi</span>
            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Riwayat Pesanan</h1>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white border border-slate-200 rounded-[40px] p-16 text-center text-slate-500 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mx-auto mb-4 text-slate-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-1.242-1.008-2.25-2.25-2.25H9m1.5-4.5h3m-9 13.5h12a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
            </svg>
            <p class="text-lg font-bold text-slate-900">Belum Ada Riwayat Pesanan</p>
            <p class="text-sm mt-1 text-slate-400">Pesanan yang Anda buat akan muncul di sini untuk pelacakan status.</p>
            <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center px-6 py-3 bg-slate-950 text-white uppercase text-xs tracking-[0.2em] rounded-xl hover:bg-slate-800 transition mt-6">Belanja Sekarang</a>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Kode Pesanan</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Metode Bayar</th>
                        <th class="px-6 py-4 text-center">Status Pesanan</th>
                        <th class="px-6 py-4 text-center">Aksi / Simulasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                            <!-- Order Code -->
                            <td class="px-6 py-6 font-bold text-slate-900 uppercase">
                                <a href="{{ route('orders.show', $order->order_code) }}" class="hover:underline text-slate-950 font-black">
                                    {{ $order->order_code }}
                                </a>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-6 text-slate-500">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-6 font-bold text-slate-900">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>

                            <!-- Payment Method -->
                            <td class="px-6 py-6 text-slate-500 uppercase font-semibold">
                                {{ $order->payment->payment_method }}
                            </td>

                            <!-- Order Status -->
                            <td class="px-6 py-6 text-center">
                                @if($order->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Pembayaran</span>
                                @elseif($order->status === 'paid')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Dibayar (Diproses)</span>
                                @elseif($order->status === 'shipped')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Dikirim</span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Selesai</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-50 text-slate-500 border border-slate-200">Dibatalkan</span>
                                @endif
                            </td>

                            <!-- Action & Simulation -->
                            <td class="px-6 py-6 text-center space-x-2">
                                <a href="{{ route('orders.show', $order->order_code) }}" class="text-xs uppercase tracking-wider font-bold text-slate-600 hover:text-slate-950">Detail</a>
                                
                                @if($order->status === 'pending')
                                    <form action="{{ route('orders.pay', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                            class="text-xs uppercase tracking-wider font-bold bg-slate-950 text-white px-3.5 py-1.5 rounded-xl hover:bg-slate-800 transition shadow-sm">
                                            Simulasi Bayar
                                        </button>
                                    </form>
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
