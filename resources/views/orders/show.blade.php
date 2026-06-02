@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_code . ' - MusicStore Luxe')

@section('content')
<div class="space-y-10 py-4">
    <!-- Breadcrumb -->
    <nav class="text-xs uppercase tracking-widest text-slate-400 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
        <span>/</span>
        <a href="{{ route('orders.history') }}" class="hover:text-slate-900">Pesanan</a>
        <span>/</span>
        <span class="text-slate-900">#{{ $order->order_code }}</span>
    </nav>

    <!-- Header info -->
    <div class="border-b border-slate-100 pb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Rincian Invoice</span>
            <h1 class="text-3xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Pesanan #{{ $order->order_code }}</h1>
            <p class="text-slate-400 text-xs mt-1">Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div>
            @if($order->status === 'pending')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-widest">Belum Dibayar</span>
            @elseif($order->status === 'paid')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-widest">Dibayar (Diproses)</span>
            @elseif($order->status === 'shipped')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-widest">Dalam Pengiriman</span>
            @elseif($order->status === 'completed')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-widest">Selesai</span>
            @else
                <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-bold bg-slate-50 text-slate-500 border border-slate-200 uppercase tracking-widest">Dibatalkan</span>
            @endif
        </div>
    </div>

    <!-- Layout Columns -->
    <div class="grid gap-10 lg:grid-cols-[1fr_380px]">
        <!-- Left Column: Shipping details, Item table -->
        <div class="space-y-8">
            <!-- Items Table -->
            <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Item Produk</th>
                            <th class="px-6 py-4 text-center">Harga</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                                <td class="px-6 py-4 flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center">
                                        <img src="{{ $item->product && $item->product->primaryImage ? $item->product->primaryImage->image : 'https://placehold.co/100' }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" />
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block text-xs uppercase">{{ $item->product_name }}</span>
                                        <span class="text-[0.65rem] text-slate-400 block mt-0.5">SKU: {{ $item->product_sku }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-600">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 text-right font-black text-slate-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Shipping Address Card -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-4">
                <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 pb-2 border-b border-slate-100">Alamat Pengiriman</h3>
                @if($order->address)
                    <div class="space-y-1.5 text-slate-700">
                        <span class="font-bold text-slate-900 text-sm block">{{ $order->address->label }}</span>
                        <p class="text-sm font-semibold">{{ $order->address->name }} ({{ $order->address->phone }})</p>
                        <p class="text-sm leading-relaxed text-slate-500">{{ $order->address->address }}</p>
                        <p class="text-sm text-slate-500">{{ $order->address->city }}, {{ $order->address->province }}, {{ $order->address->postal_code }}</p>
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">Alamat pengiriman tidak tersedia atau telah dihapus.</p>
                @endif
            </div>

            <!-- Shipment Tracking Card -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-4">
                <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 pb-2 border-b border-slate-100">Informasi Pengiriman (Kurir)</h3>
                @if($order->shipment)
                    <div class="grid gap-6 sm:grid-cols-2 text-sm">
                        <div class="space-y-2 text-slate-600">
                            <div>Kurir Pengiriman: <strong class="text-slate-900 uppercase">{{ $order->shipment->courier }} ({{ $order->shipment->service }})</strong></div>
                            <div>Ongkos Kirim: <strong class="text-slate-900">Rp {{ number_format($order->shipment->shipping_cost, 0, ',', '.') }}</strong></div>
                            <div>Nomor Resi: <strong class="text-slate-900">{{ $order->shipment->tracking_number ?: 'Belum Tersedia (Menunggu Resi)' }}</strong></div>
                        </div>
                        <div class="space-y-2 text-slate-600">
                            <div>Status Kurir: 
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $order->shipment->status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }} uppercase">
                                    {{ $order->shipment->status === 'delivered' ? 'Terkirim' : ($order->shipment->status === 'shipped' ? 'Dalam Perjalanan' : $order->shipment->status) }}
                                </span>
                            </div>
                            <div>Dikirim pada: <span class="text-slate-950 font-semibold">{{ $order->shipment->shipped_at ? $order->shipment->shipped_at->format('d M Y, H:i') : '-' }}</span></div>
                            <div>Diterima pada: <span class="text-slate-950 font-semibold">{{ $order->shipment->delivered_at ? $order->shipment->delivered_at->format('d M Y, H:i') : '-' }}</span></div>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">Informasi pengiriman tidak tersedia.</p>
                @endif
            </div>
        </div>

        <!-- Right Column: Order Summary & Payment -->
        <div class="space-y-6">
            <!-- Payment Info -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-6">
                <h3 class="text-xl font-black uppercase tracking-tight text-slate-950 pb-4 border-b border-slate-100">Status Pembayaran</h3>
                
                @if($order->payment)
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between text-slate-500">
                            <span>Metode Pembayaran</span>
                            <span class="font-bold text-slate-900 uppercase">{{ $order->payment->payment_method }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Transaction ID</span>
                            <span class="font-mono text-xs text-slate-900 font-bold">{{ $order->payment->transaction_id }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Status Bayar</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $order->payment->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} uppercase">
                                {{ $order->payment->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Waktu Lunas</span>
                            <span class="font-bold text-slate-900">{{ $order->payment->paid_at ? $order->payment->paid_at->format('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>
                @endif

                @if($order->status === 'pending')
                    <div class="pt-4 border-t border-slate-100">
                        <form action="{{ route('orders.pay', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="w-full py-3.5 bg-slate-950 text-white rounded-xl font-black uppercase text-xs tracking-[0.2em] hover:bg-slate-800 transition shadow-sm">
                                Bayar Sekarang
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Invoice Totals Card -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-4">
                <h3 class="text-lg font-black uppercase tracking-tight text-slate-950 pb-2 border-b border-slate-100">Total Invoice</h3>
                <div class="space-y-3 text-sm text-slate-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-rose-600">
                            <span>Diskon Kupon</span>
                            <span class="font-bold">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-end">
                        <span class="font-bold text-slate-900">Total Akhir</span>
                        <span class="text-xl font-black text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
