@extends('layouts.app')

@section('title', 'Kelola Pesanan #' . $order->order_code . ' - Admin DjudasMS')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-8 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Detail Pesanan</span>
            <h1 class="text-3xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Kelola Pesanan #{{ $order->order_code }}</h1>
            <p class="text-slate-400 text-xs mt-1">Oleh pelanggan: <strong class="text-slate-700">{{ $order->user->name }}</strong> ({{ $order->user->email }})</p>
        </div>
        <a href="{{ route('admin.orders') }}" class="text-xs uppercase tracking-widest font-bold text-slate-500 hover:text-slate-950 transition">← Kembali</a>
    </div>

    <!-- Layout Grid -->
    <div class="grid gap-10 lg:grid-cols-[1fr_380px]">
        <!-- Left: Order Details & Items -->
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

            <!-- Shipping address details -->
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
                    <p class="text-sm text-slate-400 italic">Alamat pengiriman tidak tersedia.</p>
                @endif
            </div>

            <!-- Shipment Details -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-4">
                <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 pb-2 border-b border-slate-100">Rincian Pengiriman</h3>
                @if($order->shipment)
                    <div class="grid gap-6 sm:grid-cols-2 text-sm text-slate-600">
                        <div class="space-y-2">
                            <div>Kurir: <strong class="text-slate-900 uppercase">{{ $order->shipment->courier }} ({{ $order->shipment->service }})</strong></div>
                            <div>Biaya Ongkir: <strong class="text-slate-900">Rp {{ number_format($order->shipment->shipping_cost, 0, ',', '.') }}</strong></div>
                        </div>
                        <div class="space-y-2">
                            <div>Status Kurir: <strong class="text-slate-900 uppercase">{{ $order->shipment->status }}</strong></div>
                            <div>No. Resi: <strong class="text-slate-900">{{ $order->shipment->tracking_number ?: 'BELUM DIINPUT' }}</strong></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Action & Totals Summary -->
        <div class="space-y-6">
            <!-- Status Information (Read-only) -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-6">
                <h3 class="text-xl font-black uppercase tracking-tight text-slate-950 pb-4 border-b border-slate-100">Status Pesanan</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 font-medium">Status Saat Ini</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-slate-100 text-slate-700">
                            {{ $order->status }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed italic">
                        Pembaruan status pesanan dan nomor resi kini dikelola sepenuhnya secara otomatis melalui Biteship API dan Webhook. Anda dapat memantau status aktual di dashboard Biteship.
                    </p>
                </div>
            </div>

            <!-- Totals Card -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-4">
                <h3 class="text-lg font-black uppercase tracking-tight text-slate-950 pb-2 border-b border-slate-100">Kalkulasi Tagihan</h3>
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
                        <span class="font-bold text-slate-900">Total Tagihan</span>
                        <span class="text-xl font-black text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
