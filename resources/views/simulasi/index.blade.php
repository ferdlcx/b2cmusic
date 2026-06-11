@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-black uppercase tracking-tight text-walnut-950">Biteship Sandbox Simulator</h1>
        <p class="text-walnut-600 mt-2">Halaman khusus untuk pengujian simulasi webhook kurir (Tanpa memotong saldo API). Tidak terhubung ke halaman Admin sesuai ketentuan sistem.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-800/10 text-emerald-800 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-800/10 text-red-800 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-walnut-800/10 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-cream-50 text-walnut-600 uppercase text-[0.65rem] tracking-widest font-bold">
                    <tr>
                        <th class="px-6 py-4">Order ID</th>
                        <th class="px-6 py-4">Pembeli</th>
                        <th class="px-6 py-4">Nomor Resi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Aksi Webhook Sandbox</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-walnut-800/5 text-walnut-950 font-medium">
                    @forelse($orders as $order)
                        <tr class="hover:bg-walnut-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono font-black">{{ $order->order_code }}</td>
                            <td class="px-6 py-4">{{ $order->user->name }}</td>
                            <td class="px-6 py-4 font-mono text-gold-600">
                                {{ $order->shipment->tracking_number ?? 'Belum ada resi' }}
                            </td>
                            <td class="px-6 py-4">
                                @if(in_array($order->status, ['paid', 'processing']))
                                    <span class="inline-flex px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold uppercase tracking-wide">Dibayar / Diproses</span>
                                @elseif($order->status === 'shipped')
                                    <span class="inline-flex px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold uppercase tracking-wide">Dalam Pengiriman</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(in_array($order->status, ['paid', 'processing']))
                                    <form action="{{ route('simulasi.ship', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold uppercase tracking-wide transition shadow-sm border border-amber-600/20">
                                            Update Status (Pick-up)
                                        </button>
                                    </form>
                                @elseif($order->status === 'shipped')
                                    <form action="{{ route('simulasi.arrive', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold uppercase tracking-wide transition shadow-sm border border-emerald-600/20">
                                            Update Status (Delivered)
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-walnut-500 font-medium">
                                Tidak ada pesanan aktif (Paid / Shipped) yang bisa disimulasikan saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
