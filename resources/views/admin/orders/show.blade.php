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
            <!-- Update Order Status Form -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-6">
                <h3 class="text-xl font-black uppercase tracking-tight text-slate-950 pb-4 border-b border-slate-100">Kelola Status</h3>
                
                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Order Status -->
                    <div class="space-y-2">
                        <label for="status" class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Status Pesanan</label>
                        <select name="status" id="status" 
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition font-bold text-slate-800">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending (Belum Bayar)</option>
                            <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid (Diproses)</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped (Dikirim)</option>
                            <option value="canceled" {{ $order->status === 'canceled' ? 'selected' : '' }}>Canceled (Batal)</option>
                        </select>
                    </div>

                    <!-- Tracking Number -->
                    <div class="space-y-2">
                        <label for="tracking_number" class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Nomor Resi Pengiriman</label>
                        <input type="text" name="tracking_number" id="tracking_number" 
                            value="{{ old('tracking_number', $order->shipment ? $order->shipment->tracking_number : '') }}" 
                            placeholder="e.g. JNE123456789"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition" />
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-slate-950 text-white rounded-xl font-black uppercase text-xs tracking-[0.2em] hover:bg-slate-800 transition shadow-sm">
                        Perbarui Status
                    </button>
                </form>

                <!-- Testing Only: Simulate Kurir Tiba -->
                @if($order->status === 'shipped' && $order->shipment && $order->shipment->status !== 'delivered')
                    <div class="mt-4 pt-4 border-t border-dashed border-sky-200">
                        <p class="text-[0.6rem] uppercase tracking-widest text-sky-600 font-bold mb-3">⚠️ Testing Mode: Simulasi Ekspedisi</p>
                        <form action="{{ route('admin.orders.arrive', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="w-full py-3 bg-sky-50 border-2 border-sky-300 text-sky-700 rounded-xl font-bold uppercase text-[0.65rem] tracking-widest hover:bg-sky-100 transition flex items-center justify-center gap-2">
                                <i data-lucide="map-pin" class="w-4 h-4"></i> [SIMULASI] Paket Tiba di Tujuan
                            </button>
                        </form>
                    </div>
                @endif
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
