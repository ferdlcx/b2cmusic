@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_code . ' - MusicStore Luxe')

@section('content')
<div class="space-y-10 py-4">
    <!-- Breadcrumb -->
    <nav class="text-xs font-semibold uppercase tracking-wider text-slate-400 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-indigo-600 flex items-center gap-1 transition">
            <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
        </a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="{{ route('orders.history') }}" class="hover:text-indigo-600 transition">Pesanan</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-900 font-bold">#{{ $order->order_code }}</span>
    </nav>

    <!-- Header info -->
    <div class="border-b border-slate-200/60 pb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Rincian Invoice</span>
            <h1 class="font-display text-3xl font-black uppercase tracking-tight text-slate-950">Pesanan #{{ $order->order_code }}</h1>
            <p class="text-slate-400 text-xs font-semibold">Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
        <div>
            @if($order->status === 'pending')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Belum Dibayar
                </span>
            @elseif($order->status === 'paid')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Dibayar
                </span>
            @elseif($order->status === 'processing')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                    Diproses
                </span>
            @elseif($order->status === 'shipped')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-bounce"></span>
                    Dikirim
                </span>
            @elseif($order->status === 'completed')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-teal-50 text-teal-700 border border-teal-100 uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                    Selesai
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-slate-50 text-slate-500 border border-slate-200 uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    Dibatalkan
                </span>
            @endif
        </div>
    </div>

    <!-- Layout Columns -->
    <div class="grid gap-10 lg:grid-cols-[1fr_380px]">
        <!-- Left Column: Shipping details, Item table -->
        <div class="space-y-8">
            <!-- Items Table -->
            <div class="overflow-hidden rounded-[32px] border border-slate-200/80 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-200/60">
                            <tr>
                                <th class="px-6 py-4.5 font-bold">Item Produk</th>
                                <th class="px-6 py-4.5 font-bold text-center">Harga</th>
                                <th class="px-6 py-4.5 font-bold text-center">Jumlah</th>
                                <th class="px-6 py-4.5 font-bold text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/40 transition">
                                    <td class="px-6 py-4.5 flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center">
                                            <img src="{{ $item->product && $item->product->primaryImage ? $item->product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=80&q=80' }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" />
                                        </div>
                                        <div class="space-y-0.5">
                                            <span class="font-display font-bold text-slate-900 block text-xs uppercase tracking-tight">{{ $item->product_name }}</span>
                                            <span class="text-[0.6rem] text-slate-400 block font-semibold">SKU: {{ $item->product_sku }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4.5 text-center text-slate-600 font-semibold text-xs">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4.5 text-center font-bold text-slate-900 text-xs">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4.5 text-right font-black text-slate-900 text-xs">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Shipping Address Card -->
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i data-lucide="map-pin" class="w-5 h-5 text-indigo-600"></i>
                    <h3 class="font-display text-base font-bold uppercase tracking-tight text-slate-950">Alamat Pengiriman</h3>
                </div>
                @if($order->address)
                    <div class="space-y-2 text-slate-700 text-xs">
                        <span class="font-bold text-indigo-600 uppercase tracking-wider block">{{ $order->address->label }}</span>
                        <p class="font-semibold text-slate-900 text-sm">{{ $order->address->name }} ({{ $order->address->phone }})</p>
                        <p class="leading-relaxed text-slate-500 font-normal">{{ $order->address->address }}</p>
                        <p class="text-slate-500 font-normal">{{ $order->address->city }}, {{ $order->address->province }}, {{ $order->address->postal_code }}</p>
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Alamat pengiriman tidak tersedia atau telah dihapus.</p>
                @endif
            </div>

            <!-- Shipment Tracking Card -->
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i data-lucide="truck" class="w-5 h-5 text-indigo-600"></i>
                    <h3 class="font-display text-base font-bold uppercase tracking-tight text-slate-950">Informasi Pengiriman</h3>
                </div>
                @if($order->shipment)
                    <div class="grid gap-6 sm:grid-cols-2 text-xs font-semibold">
                        <div class="space-y-3 text-slate-500">
                            <div>Kurir Pengiriman: <strong class="text-slate-900 uppercase font-black ml-1">{{ $order->shipment->courier }} ({{ $order->shipment->service }})</strong></div>
                            <div>Ongkos Kirim: <strong class="text-slate-900 ml-1">Rp {{ number_format($order->shipment->shipping_cost, 0, ',', '.') }}</strong></div>
                            <div>Nomor Resi: <strong class="text-slate-900 ml-1">{{ $order->shipment->tracking_number ?: 'Belum Tersedia (Menunggu Resi)' }}</strong></div>
                        </div>
                        <div class="space-y-3 text-slate-500">
                            <div>Status Ekspedisi: 
                                <span class="px-2.5 py-0.5 rounded-xl text-[0.65rem] font-bold {{ $order->shipment->status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }} uppercase ml-1">
                                    {{ $order->shipment->status === 'delivered' ? 'Terkirim' : ($order->shipment->status === 'shipped' ? 'Dalam Perjalanan' : $order->shipment->status) }}
                                </span>
                            </div>
                            <div>Tanggal Kirim: <span class="text-slate-950 font-bold ml-1">{{ $order->shipment->shipped_at ? $order->shipment->shipped_at->format('d M Y, H:i') . ' WIB' : '-' }}</span></div>
                            <div>Tanggal Terima: <span class="text-slate-950 font-bold ml-1">{{ $order->shipment->delivered_at ? $order->shipment->delivered_at->format('d M Y, H:i') . ' WIB' : '-' }}</span></div>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Informasi pengiriman tidak tersedia.</p>
                @endif
            </div>
        </div>

        <!-- Right Column: Order Summary & Payment -->
        <div class="space-y-6">
            <!-- Payment Info -->
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-6">
                <div class="flex items-center gap-2 pb-4 border-b border-slate-100">
                    <i data-lucide="wallet" class="w-5 h-5 text-indigo-600"></i>
                    <h3 class="font-display text-lg font-bold uppercase tracking-tight text-slate-950">Status Pembayaran</h3>
                </div>
                
                @if($order->payment)
                    <div class="space-y-4 text-xs font-semibold text-slate-500">
                        <div class="flex justify-between">
                            <span>Metode Pembayaran</span>
                            <span class="font-bold text-slate-900 uppercase">{{ $order->payment->payment_method }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>ID Transaksi</span>
                            <span class="font-mono text-[0.65rem] text-slate-900 font-bold truncate max-w-[150px]" title="{{ $order->payment->transaction_id }}">
                                {{ $order->payment->transaction_id }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status Bayar</span>
                            <span class="px-2.5 py-0.5 rounded-xl text-[0.65rem] font-bold {{ $order->payment->status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }} uppercase">
                                {{ $order->payment->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Waktu Lunas</span>
                            <span class="font-bold text-slate-900">{{ $order->payment->paid_at ? $order->payment->paid_at->format('d M Y, H:i') . ' WIB' : '-' }}</span>
                        </div>
                    </div>
                @endif

                @if($order->status === 'pending')
                    <div class="pt-4 border-t border-slate-100">
                        @if($order->payment && $order->payment->snap_token)
                            <button type="button" id="pay-button"
                                class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                                <i data-lucide="credit-card" class="w-4 h-4"></i> Bayar via Midtrans
                            </button>
                            
                            <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
                            <script>
                                document.getElementById('pay-button').onclick = function(){
                                    snap.pay('{{ $order->payment->snap_token }}', {
                                        onSuccess: function(result){
                                            window.location.href = "{{ route('orders.show', $order->order_code) }}?pay_success=true";
                                        },
                                        onPending: function(result){
                                            window.location.href = "{{ route('orders.show', $order->order_code) }}";
                                        },
                                        onError: function(result){
                                            window.location.href = "{{ route('orders.show', $order->order_code) }}?pay_error=true";
                                        }
                                    });
                                };
                            </script>
                        @else
                            <form action="{{ route('orders.pay', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                    class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                                    <i data-lucide="shield-alert" class="w-4 h-4"></i> Simulasi Bayar Lokal
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Invoice Totals Card -->
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-4">
                <h3 class="font-display text-base font-bold uppercase tracking-tight text-slate-950 pb-2 border-b border-slate-100">Total Invoice</h3>
                <div class="space-y-3.5 text-xs font-semibold text-slate-500">
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
                        <span class="font-bold text-slate-950">Total Akhir</span>
                        <span class="text-2xl font-black text-indigo-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
