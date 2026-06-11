@extends('layouts.app')

@section('title', 'Kelola Pesanan #' . $order->order_code . ' - Admin DjudasMS')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-8 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-muted font-bold">Detail Pesanan</span>
            <h1 class="text-3xl font-black uppercase tracking-[-0.04em] text-walnut-950 mt-3">Kelola Pesanan #{{ $order->order_code }}</h1>
            <p class="text-walnut-400 text-xs mt-1">Oleh pelanggan: <strong class="text-walnut-800">{{ $order->user->name }}</strong> ({{ $order->user->email }})</p>
        </div>
        <a href="{{ route('admin.orders') }}" class="text-xs uppercase tracking-widest font-bold text-muted hover:text-walnut-950 transition">← Kembali</a>
    </div>

    <!-- Layout Grid -->
    <div class="grid gap-10 lg:grid-cols-[1fr_380px]">
        <!-- Left: Order Details & Items -->
        <div class="space-y-8">
            <!-- Items Table -->
            <div class="overflow-hidden rounded-[32px] border border-walnut-800/10 bg-cream-50 shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase tracking-widest text-walnut-400 bg-cream-100 border-b border-walnut-800/10">
                        <tr>
                            <th class="px-6 py-4">Item Produk</th>
                            <th class="px-6 py-4 text-center">Harga</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-100/50">
                                <td class="px-6 py-4 flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-cream-100 border border-walnut-800/10 flex-shrink-0 flex items-center justify-center">
                                        <img src="{{ $item->product && $item->product->primaryImage ? $item->product->primaryImage->image : 'https://placehold.co/100' }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" />
                                    </div>
                                    <div>
                                        <span class="font-bold text-walnut-900 block text-xs uppercase">{{ $item->product_name }}</span>
                                        <span class="text-[0.65rem] text-walnut-400 block mt-0.5">SKU: {{ $item->product_sku }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-walnut-600">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-walnut-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 text-right font-black text-walnut-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Shipping address details -->
            <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-8 shadow-sm space-y-4">
                <h3 class="text-lg font-black uppercase tracking-tight text-walnut-900 pb-2 border-b border-walnut-800/10">Alamat Pengiriman</h3>
                @if($order->address)
                    <div class="space-y-1.5 text-walnut-800">
                        <span class="font-bold text-walnut-900 text-sm block">{{ $order->address->label }}</span>
                        <p class="text-sm font-semibold">{{ $order->address->name }} ({{ $order->address->phone }})</p>
                        <p class="text-sm leading-relaxed text-muted">{{ $order->address->address }}</p>
                        <p class="text-sm text-muted">{{ $order->address->city }}, {{ $order->address->province }}, {{ $order->address->postal_code }}</p>
                    </div>
                @else
                    <p class="text-sm text-walnut-400 italic">Alamat pengiriman tidak tersedia.</p>
                @endif
            </div>

            <!-- Shipment Details -->
            <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-8 shadow-sm space-y-4">
                <h3 class="text-lg font-black uppercase tracking-tight text-walnut-900 pb-2 border-b border-walnut-800/10">Rincian Pengiriman</h3>
                @if($order->shipment)
                    <div class="grid gap-6 sm:grid-cols-2 text-sm text-walnut-600">
                        <div class="space-y-2">
                            <div>Kurir: <strong class="text-walnut-900 uppercase">{{ $order->shipment->courier }} ({{ $order->shipment->service }})</strong></div>
                            <div>Biaya Ongkir: <strong class="text-walnut-900">Rp {{ number_format($order->shipment->shipping_cost, 0, ',', '.') }}</strong></div>
                        </div>
                        <div class="space-y-2">
                            <div>Status Kurir: <strong class="text-walnut-900 uppercase">{{ $order->shipment->status }}</strong></div>
                            <div>No. Resi: <strong class="text-walnut-900">{{ $order->shipment->tracking_number ?: 'BELUM DIINPUT' }}</strong></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Action & Totals Summary -->
        <div class="space-y-6">
            <!-- Status Information (Interactive) -->
            <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-8 shadow-sm space-y-6">
                <h3 class="text-xl font-black uppercase tracking-tight text-walnut-950 pb-4 border-b border-walnut-800/10">Kelola Status Pesanan</h3>
                
                <div class="space-y-4">
                    <div class="p-4 bg-cream-100 border border-walnut-800/10 rounded-xl text-center">
                        <p class="text-[0.65rem] text-muted font-bold uppercase tracking-widest mb-1">Nomor Resi / AWB Otomatis</p>
                        <p class="font-mono font-black text-lg text-walnut-950">{{ $order->shipment && $order->shipment->tracking_number ? $order->shipment->tracking_number : 'Menunggu Resi...' }}</p>
                    </div>

                    <div class="text-[0.65rem] text-muted leading-relaxed font-medium bg-walnut-50 border border-walnut-800/10 p-3 rounded-lg">
                        <i data-lucide="info" class="w-3 h-3 inline mr-1 text-gold-600"></i>
                        Status pesanan akan <strong>otomatis berubah menjadi "Shipped"</strong> saat kurir Biteship memindai paket (Pick-up). Tidak perlu diubah manual.
                    </div>

                    <a href="{{ route('admin.orders.print_label', $order->id) }}" target="_blank" class="w-full py-3 bg-walnut-950 text-white rounded-xl font-bold uppercase text-[0.7rem] tracking-widest hover:bg-walnut-800 transition duration-300 flex items-center justify-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i> Cetak Label Pengiriman
                    </a>

                    @if(in_array($order->status, ['paid', 'processing']))
                        <div class="pt-4 mt-4 border-t border-walnut-800/10">
                            <form action="{{ route('admin.orders.ship', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 bg-transparent border-2 border-dashed border-walnut-800/20 text-walnut-600 rounded-xl font-bold uppercase text-[0.65rem] tracking-widest hover:border-walnut-800 hover:text-walnut-950 transition duration-300 flex items-center justify-center gap-2">
                                    <i data-lucide="test-tube" class="w-4 h-4"></i> Simulasi: Paket Di-pickup (Sandbox)
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($order->status === 'shipped')
                        <div class="pt-4 mt-4 border-t border-walnut-800/10">
                            <form action="{{ route('admin.orders.force_delivered', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 bg-transparent border-2 border-dashed border-emerald-800/20 text-emerald-600 rounded-xl font-bold uppercase text-[0.65rem] tracking-widest hover:border-emerald-800 hover:text-emerald-900 transition duration-300 flex items-center justify-center gap-2">
                                    <i data-lucide="test-tube" class="w-4 h-4"></i> Simulasi: Paket Tiba (Sandbox)
                                </button>
                            </form>
                        </div>
                    @endif
                </div>


            </div>

            <!-- Totals Card -->
            <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-8 shadow-sm space-y-4">
                <h3 class="text-lg font-black uppercase tracking-tight text-walnut-950 pb-2 border-b border-walnut-800/10">Kalkulasi Tagihan</h3>
                <div class="space-y-3 text-sm text-walnut-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-bold text-walnut-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="font-bold text-walnut-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-rose-600">
                            <span>Diskon Kupon</span>
                            <span class="font-bold">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="pt-3 border-t border-walnut-800/10 flex justify-between items-end">
                        <span class="font-bold text-walnut-900">Total Tagihan</span>
                        <span class="text-xl font-black text-walnut-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
