@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_code . ' - DjudasMS')

@section('content')
<div class="space-y-12 py-8">
    <!-- Breadcrumb -->
    <nav class="text-[0.65rem] font-bold uppercase tracking-widest text-muted flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-gold-600 flex items-center gap-1 transition">
            Home
        </a>
        <span class="text-walnut-800/20">/</span>
        <a href="{{ route('orders.history') }}" class="hover:text-gold-600 transition">Pesanan</a>
        <span class="text-walnut-800/20">/</span>
        <span class="text-walnut-950 font-bold">#{{ $order->order_code }}</span>
    </nav>

    <!-- Header info -->
    <div class="border-b border-walnut-800/10 pb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Rincian Invoice</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Pesanan <span class="text-gold-500">#{{ $order->order_code }}</span></h1>
            <p class="text-muted text-[0.8rem] font-medium pt-1">Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
        <div>
            @if($order->status === 'pending')
                <span class="inline-block px-4 py-2 border border-walnut-500 text-[0.65rem] font-bold uppercase tracking-widest text-walnut-500">
                    Menunggu Pembayaran
                </span>
            @elseif($order->status === 'paid')
                <span class="inline-block px-4 py-2 bg-walnut-900 text-gold-500 text-[0.65rem] font-bold uppercase tracking-widest">
                    Sudah Dibayar
                </span>
            @elseif($order->status === 'processing')
                <span class="inline-block px-4 py-2 border border-walnut-900 text-[0.65rem] font-bold uppercase tracking-widest text-walnut-900">
                    Diproses
                </span>
            @elseif($order->status === 'shipped')
                <span class="inline-block px-4 py-2 bg-cream-100 border border-gold-500 text-[0.65rem] font-bold uppercase tracking-widest text-gold-600">
                    Dikirim
                </span>
            @elseif($order->status === 'completed')
                <span class="inline-block px-4 py-2 bg-walnut-900 text-gold-500 text-[0.65rem] font-bold uppercase tracking-widest">
                    Selesai
                </span>
            @else
                <span class="inline-block px-4 py-2 border border-red-600 text-[0.65rem] font-bold uppercase tracking-widest text-red-600">
                    Dibatalkan
                </span>
            @endif
        </div>
    </div>

    <!-- Layout Columns -->
    <div class="grid gap-12 lg:grid-cols-[1fr_380px]">
        <!-- Left Column: Shipping details, Item table -->
        <div class="space-y-12">
            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[0.65rem] uppercase tracking-widest text-muted border-b border-walnut-800/20">
                        <tr>
                            <th class="pb-4 font-bold">Item Produk</th>
                            <th class="pb-4 font-bold text-center">Harga</th>
                            <th class="pb-4 font-bold text-center">Jumlah</th>
                            <th class="pb-4 font-bold text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-50 transition">
                                <td class="py-6 flex items-center gap-6">
                                    <div class="w-16 h-16 bg-cream-50 border border-walnut-800/10 flex-shrink-0">
                                        <img src="{{ $item->product && $item->product->primaryImage ? $item->product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=80&q=80' }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover mix-blend-multiply opacity-90" />
                                    </div>
                                    <div class="space-y-1">
                                        <span class="font-display font-black text-[0.9rem] text-walnut-950 uppercase tracking-tight">{{ $item->product_name }}</span>
                                        <span class="text-[0.65rem] uppercase tracking-widest text-muted block font-bold">SKU: {{ $item->product_sku }}</span>
                                    </div>
                                </td>
                                <td class="py-6 text-center text-muted font-bold text-[0.75rem] tracking-widest">
                                    IDR {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="py-6 text-center font-bold text-walnut-950 text-[0.8rem]">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-6 text-right font-black text-walnut-900 text-[0.8rem] tracking-widest">
                                    IDR {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Shipping Address Card -->
            <div class="bg-transparent border border-walnut-800/10 p-8 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-walnut-800/10">
                    <i data-lucide="map-pin" class="w-5 h-5 text-gold-600"></i>
                    <h3 class="font-display text-xl font-black uppercase tracking-tighter text-walnut-950">Alamat Pengiriman</h3>
                </div>
                @if($order->address)
                    <div class="space-y-2 text-muted text-[0.8rem] font-medium">
                        <span class="font-bold text-gold-600 text-[0.65rem] uppercase tracking-widest block mb-3">{{ $order->address->label }}</span>
                        <p class="font-bold text-walnut-950">{{ $order->address->name }} ({{ $order->address->phone }})</p>
                        <p class="leading-relaxed">{{ $order->address->address }}</p>
                        <p>{{ $order->address->city }}, {{ $order->address->province }}, {{ $order->address->postal_code }}</p>
                    </div>
                @else
                    <p class="text-[0.75rem] text-muted italic font-medium">Alamat pengiriman tidak tersedia atau telah dihapus.</p>
                @endif
            </div>

            <!-- Shipment Tracking Card -->
            <div class="bg-transparent border border-walnut-800/10 p-8 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-walnut-800/10">
                    <i data-lucide="truck" class="w-5 h-5 text-gold-600"></i>
                    <h3 class="font-display text-xl font-black uppercase tracking-tighter text-walnut-950">Informasi Pengiriman</h3>
                </div>
                @if($order->shipment)
                    <div class="grid gap-8 sm:grid-cols-2 text-[0.75rem] font-medium">
                        <div class="space-y-4 text-muted">
                            <div class="flex flex-col gap-1">
                                <span class="text-[0.65rem] uppercase tracking-widest font-bold">Kurir Pengiriman</span>
                                <strong class="text-walnut-950 uppercase font-black tracking-wider">{{ $order->shipment->courier }} ({{ $order->shipment->service }})</strong>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[0.65rem] uppercase tracking-widest font-bold">Ongkos Kirim</span>
                                <strong class="text-walnut-950 tracking-widest">IDR {{ number_format($order->shipment->shipping_cost, 0, ',', '.') }}</strong>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[0.65rem] uppercase tracking-widest font-bold">Nomor Resi</span>
                                <strong class="text-walnut-950 tracking-wider">{{ $order->shipment->tracking_number ?: 'Belum Tersedia' }}</strong>
                            </div>
                        </div>
                        <div class="space-y-4 text-muted">
                            <div class="flex flex-col gap-1">
                                <span class="text-[0.65rem] uppercase tracking-widest font-bold">Status Ekspedisi</span>
                                <span class="inline-block self-start px-2 py-1 text-[0.6rem] font-bold {{ $order->shipment->status === 'delivered' ? 'bg-walnut-900 text-gold-500' : 'border border-walnut-900 text-walnut-900' }} uppercase tracking-widest">
                                    {{ $order->shipment->status === 'delivered' ? 'Terkirim' : ($order->shipment->status === 'shipped' ? 'Dalam Perjalanan' : $order->shipment->status) }}
                                </span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[0.65rem] uppercase tracking-widest font-bold">Tanggal Kirim</span>
                                <span class="text-walnut-950 font-bold">{{ $order->shipment->shipped_at ? $order->shipment->shipped_at->format('d M Y, H:i') . ' WIB' : '-' }}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[0.65rem] uppercase tracking-widest font-bold">Tanggal Terima</span>
                                <span class="text-walnut-950 font-bold">{{ $order->shipment->delivered_at ? $order->shipment->delivered_at->format('d M Y, H:i') . ' WIB' : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sandbox Simulate Button -->
                    @if($order->shipment->status === 'shipped')
                    <div class="mt-8 pt-6 border-t border-walnut-800/10">
                        <form action="{{ route('orders.sandboxArrive', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Simulasi: Ubah status menjadi Terkirim (Barang Sampai)?');" class="w-full py-3 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.65rem] tracking-widest hover:bg-gold-600 hover:text-white transition duration-300 flex justify-center items-center gap-2">
                                <i data-lucide="package-check" class="w-4 h-4"></i> Simulasikan Barang Sampai (Sandbox)
                            </button>
                        </form>
                    </div>
                    @endif
                    
                    <!-- Tracking Timeline -->
                    <div class="mt-8 pt-6 border-t border-walnut-800/10 space-y-6 relative before:absolute before:inset-0 before:ml-[11px] before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-walnut-800/20 before:to-transparent">
                        
                        <!-- Dikemas -->
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <div class="flex items-center justify-center w-6 h-6 rounded-full border-4 border-cream-50 bg-gold-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10"></div>
                            <div class="w-[calc(100%-2rem)] md:w-[calc(50%-1.5rem)] p-4 rounded bg-cream-50 border border-walnut-800/10 shadow-sm">
                                <div class="flex items-center justify-between space-x-2 mb-1">
                                    <div class="font-bold text-walnut-950 text-[0.75rem] uppercase tracking-widest">Pesanan Dikemas</div>
                                    <time class="text-[0.65rem] font-bold text-gold-600">{{ $order->created_at->format('d M, H:i') }}</time>
                                </div>
                                <div class="text-muted text-[0.7rem] font-medium">Penjual sedang menyiapkan pesanan Anda.</div>
                            </div>
                        </div>

                        <!-- Dikirim -->
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group {{ in_array($order->shipment->status, ['shipped', 'delivered']) ? 'is-active' : 'opacity-50' }}">
                            <div class="flex items-center justify-center w-6 h-6 rounded-full border-4 border-cream-50 {{ in_array($order->shipment->status, ['shipped', 'delivered']) ? 'bg-gold-500 text-white' : 'bg-walnut-800/30' }} shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10"></div>
                            <div class="w-[calc(100%-2rem)] md:w-[calc(50%-1.5rem)] p-4 rounded bg-cream-50 border border-walnut-800/10 shadow-sm">
                                <div class="flex items-center justify-between space-x-2 mb-1">
                                    <div class="font-bold text-walnut-950 text-[0.75rem] uppercase tracking-widest">Pesanan Dikirim</div>
                                    @if($order->shipment->shipped_at)
                                        <time class="text-[0.65rem] font-bold text-gold-600">{{ $order->shipment->shipped_at->format('d M, H:i') }}</time>
                                    @endif
                                </div>
                                <div class="text-muted text-[0.7rem] font-medium">Pesanan telah diserahkan ke pihak logistik ({{ $order->shipment->courier }}).</div>
                            </div>
                        </div>

                        <!-- Tiba di Tujuan -->
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group {{ $order->shipment->status === 'delivered' ? 'is-active' : 'opacity-50' }}">
                            <div class="flex items-center justify-center w-6 h-6 rounded-full border-4 border-cream-50 {{ $order->shipment->status === 'delivered' ? 'bg-gold-500 text-white' : 'bg-walnut-800/30' }} shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10"></div>
                            <div class="w-[calc(100%-2rem)] md:w-[calc(50%-1.5rem)] p-4 rounded bg-cream-50 border border-walnut-800/10 shadow-sm">
                                <div class="flex items-center justify-between space-x-2 mb-1">
                                    <div class="font-bold text-walnut-950 text-[0.75rem] uppercase tracking-widest">Pesanan Tiba</div>
                                    @if($order->shipment->delivered_at)
                                        <time class="text-[0.65rem] font-bold text-gold-600">{{ $order->shipment->delivered_at->format('d M, H:i') }}</time>
                                    @endif
                                </div>
                                <div class="text-muted text-[0.7rem] font-medium">Pesanan telah diterima oleh yang bersangkutan.</div>
                            </div>
                        </div>

                    </div>
                @else
                    <p class="text-[0.75rem] text-muted italic font-medium">Informasi pengiriman tidak tersedia.</p>
                @endif
            </div>
        </div>

        <!-- Right Column: Order Summary & Payment -->
        <div class="space-y-8">
            <!-- Payment Info -->
            <div class="bg-cream-50 border border-walnut-800/10 p-8 space-y-6">
                <div class="pb-4 border-b border-walnut-800/10">
                    <h3 class="font-display text-xl font-black uppercase tracking-tighter text-walnut-950">Status Pembayaran</h3>
                </div>
                
                @if($order->payment)
                    <div class="space-y-5 text-[0.75rem] font-bold text-muted">
                        <div class="flex justify-between items-center">
                            <span class="uppercase tracking-widest text-[0.65rem]">Metode</span>
                            <span class="text-walnut-950 uppercase tracking-widest">{{ $order->payment->payment_method }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="uppercase tracking-widest text-[0.65rem]">Transaksi</span>
                            <span class="font-mono text-[0.65rem] text-walnut-950 truncate max-w-[150px]" title="{{ $order->payment->transaction_id }}">
                                {{ $order->payment->transaction_id }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="uppercase tracking-widest text-[0.65rem]">Status</span>
                            <span class="px-2 py-1 text-[0.6rem] {{ $order->payment->status === 'paid' ? 'bg-walnut-900 text-gold-500' : 'border border-walnut-500 text-walnut-500' }} uppercase tracking-widest">
                                {{ $order->payment->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="uppercase tracking-widest text-[0.65rem]">Waktu Lunas</span>
                            <span class="text-walnut-950">{{ $order->payment->paid_at ? $order->payment->paid_at->format('d M Y, H:i') . ' WIB' : '-' }}</span>
                        </div>
                    </div>
                @endif

                @if($order->status === 'pending')
                    <div class="pt-6 border-t border-walnut-800/10">
                        @if($order->payment && $order->payment->snap_token)
                            <button type="button" id="pay-button"
                                class="w-full py-4 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.65rem] tracking-widest hover:bg-gold-600 hover:text-white transition duration-500">
                                Bayar via Midtrans
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
                                // Auto-open payment popup for QRIS/eWallet
                                @if(in_array($order->payment->payment_method, ['qris', 'ewallet']))
                                    document.addEventListener('DOMContentLoaded', function() {
                                        setTimeout(function() {
                                            document.getElementById('pay-button').click();
                                        }, 800);
                                    });
                                @endif
                            </script>

                            <form action="{{ route('orders.checkStatus', $order->id) }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" 
                                    class="w-full py-3 bg-transparent border border-walnut-800/20 text-walnut-900 font-bold uppercase text-[0.6rem] tracking-widest hover:border-gold-500 hover:text-gold-600 transition duration-300">
                                    Cek Status Pembayaran (Manual)
                                </button>
                            </form>
                        @else
                            <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-[0.75rem] flex flex-col gap-2 font-medium">
                                <span class="font-bold flex items-center gap-2"><i data-lucide="alert-circle" class="w-4 h-4"></i> Gagal Memuat</span>
                                <p>Terjadi kesalahan saat memuat link pembayaran Midtrans. Silakan hubungi kami.</p>
                            </div>
                        @endif
                    </div>
                @elseif(in_array($order->status, ['processing', 'shipped', 'completed']))
                    <div class="pt-6 border-t border-walnut-800/10">
                        <a href="{{ route('orders.track', $order->id) }}" 
                            class="block text-center w-full py-4 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.65rem] tracking-widest hover:bg-gold-600 hover:text-white transition duration-500">
                            Lacak Pengiriman Pesanan
                        </a>
                    </div>
                @endif
            </div>

            @if($order->status === 'pending')
                <div class="pt-2" x-data="{ showCancelModal: false }">
                    <button @click="showCancelModal = true" 
                        class="w-full py-3.5 bg-transparent border border-red-600 text-red-600 font-bold uppercase text-[0.65rem] tracking-widest hover:bg-red-600 hover:text-white transition duration-300">
                        Batalkan Pesanan
                    </button>
                    
                    <!-- Cancel Confirmation Modal -->
                    <div x-show="showCancelModal" x-cloak
                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                        x-transition.opacity>
                        <div class="fixed inset-0 bg-walnut-950/80" @click="showCancelModal = false"></div>
                        <div class="relative bg-cream-50 p-10 max-w-md w-full border border-walnut-800/10 space-y-8 z-10"
                            x-transition>
                            <div class="text-center space-y-4">
                                <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950">Batal Pesanan?</h3>
                                <p class="text-[0.8rem] text-muted font-medium">Apakah Anda yakin ingin membatalkan pesanan <strong>#{{ $order->order_code }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                            <div class="flex flex-col gap-4">
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-4 bg-red-600 text-white font-bold text-[0.7rem] uppercase tracking-widest hover:bg-red-700 transition">Ya, Batalkan</button>
                                </form>
                                <button @click="showCancelModal = false" class="w-full py-3 border border-walnut-800/20 text-walnut-900 font-bold text-[0.7rem] uppercase tracking-widest hover:border-walnut-900 transition">Tidak Jadi</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($order->status === 'shipped')
                <div class="pt-2 border border-walnut-800/10 p-6 bg-cream-50 mt-6 space-y-4">
                    <p class="text-[0.65rem] text-red-600 font-bold uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i> Perhatian PENTING
                    </p>
                    <p class="text-[0.7rem] text-muted font-medium leading-relaxed">
                        Pastikan paket telah Anda terima dalam kondisi baik. <strong>Tanpa melampirkan video unboxing, semua bentuk komplain atau pengajuan retur akan ditolak otomatis oleh sistem.</strong>
                    </p>

                    @if($order->shipment && $order->shipment->status === 'delivered')
                        {{-- Both buttons ACTIVE --}}
                        <div class="flex gap-4">
                            <form action="{{ route('orders.delivered', $order->id) }}" method="POST" class="flex-1" onsubmit="return confirm('PENTING: Apakah Anda sudah merekam video unboxing dan yakin ingin menyelesaikan pesanan ini?');">
                                @csrf
                                <button type="submit" 
                                    class="w-full py-4 bg-walnut-900 text-gold-500 hover:bg-gold-600 hover:text-white font-bold uppercase text-[0.7rem] tracking-widest transition duration-300">
                                    Konfirmasi Pesanan Diterima
                                </button>
                            </form>
                            <a href="{{ route('returns.create', $order->id) }}" 
                                class="flex-1 flex items-center justify-center py-4 border border-red-600 text-red-600 hover:bg-red-600 hover:text-white font-bold uppercase text-[0.7rem] tracking-widest transition duration-300">
                                Ajukan Pengembalian
                            </a>
                        </div>
                    @else
                        {{-- Both buttons DISABLED --}}
                        <div class="flex gap-4">
                            <button type="button" disabled
                                class="flex-1 py-4 bg-walnut-900/50 text-gold-500/50 font-bold uppercase text-[0.7rem] tracking-widest cursor-not-allowed pointer-events-none">
                                Konfirmasi Pesanan Diterima
                            </button>
                            <button type="button" disabled
                                class="flex-1 py-4 bg-walnut-800/10 border border-walnut-800/20 text-muted font-bold uppercase text-[0.7rem] tracking-widest cursor-not-allowed pointer-events-none">
                                Ajukan Pengembalian
                            </button>
                        </div>
                        <p class="text-[0.7rem] text-muted font-medium text-center italic">
                            Tombol akan aktif setelah status pengiriman berubah menjadi "Tiba di Tujuan".
                        </p>
                    @endif
                </div>
            @endif

            @if($order->status === 'completed' && $order->updated_at->addDays(30)->isFuture())
                <div class="pt-2 space-y-3">
                    <a href="{{ route('returns.create', $order->id) }}" 
                        class="block text-center w-full py-3.5 bg-transparent border border-red-600 text-red-600 font-bold uppercase text-[0.65rem] tracking-widest hover:bg-red-600 hover:text-white transition duration-300">
                        Ajukan Retur Barang
                    </a>
                    @php
                        $daysRemaining = max(0, 30 - $order->updated_at->diffInDays(now()));
                    @endphp
                    <p class="text-[0.55rem] text-muted font-bold text-center uppercase tracking-widest">
                        Garansi Retur: Sisa {{ $daysRemaining }} Hari
                    </p>
                </div>
            @endif

            <!-- Invoice Totals Card -->
            <div class="bg-transparent border border-walnut-800/10 p-8 space-y-6">
                <div class="pb-4 border-b border-walnut-800/10">
                    <h3 class="font-display text-xl font-black uppercase tracking-tighter text-walnut-950">Total Invoice</h3>
                </div>
                <div class="space-y-4 text-[0.75rem] font-bold text-muted tracking-widest">
                    <div class="flex justify-between items-center">
                        <span class="uppercase text-[0.65rem]">Subtotal</span>
                        <span class="text-walnut-950">IDR {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="uppercase text-[0.65rem]">Ongkos Kirim</span>
                        <span class="text-walnut-950">IDR {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between items-center text-red-600">
                            <span class="uppercase text-[0.65rem]">Diskon</span>
                            <span>-IDR {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="pt-6 border-t border-walnut-800/10 flex justify-between items-end">
                        <span class="uppercase text-[0.65rem] text-walnut-950">Total Akhir</span>
                        <span class="text-xl font-black text-gold-600 tracking-tight">IDR {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
