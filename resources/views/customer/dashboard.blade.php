@extends('layouts.app')

@section('title', 'Dashboard Saya - DjudasMS')

@section('content')
<div class="space-y-12 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-walnut-800/10">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Area Anggota</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Dashboard.</h1>
            <p class="text-sm text-muted font-medium pt-2">Halo, <span class="font-bold text-walnut-950">{{ auth()->user()->name }}</span>! Kelola profil dan pantau riwayat koleksi Anda.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('returns.index') }}" class="inline-flex items-center px-4 py-2 border border-walnut-800/20 bg-transparent text-[0.65rem] font-bold uppercase tracking-widest text-walnut-900 hover:border-gold-500 hover:text-gold-600 transition">
                Riwayat Retur
            </a>
            <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2 border border-walnut-800/20 bg-transparent text-[0.65rem] font-bold uppercase tracking-widest text-walnut-900 hover:border-gold-500 hover:text-gold-600 transition">
                Profil
            </a>
            <a href="{{ route('catalog') }}" class="inline-flex items-center px-5 py-2 bg-walnut-900 text-gold-500 text-[0.65rem] font-bold uppercase tracking-widest hover:bg-gold-600 hover:text-white transition duration-300">
                Lanjut Belanja
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Wishlist -->
        <div class="bg-cream-50 border border-walnut-800/10 p-6 flex items-center justify-between group hover:border-gold-500 transition">
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-muted font-bold">Wishlist</p>
                <p class="text-3xl font-display font-black text-walnut-950 mt-1">{{ $wishlistCount }}</p>
                <a href="{{ route('wishlist.index') }}" class="text-[0.65rem] text-gold-600 uppercase tracking-widest font-bold hover:text-walnut-950 block mt-2">Lihat &rarr;</a>
            </div>
            <i data-lucide="heart" class="w-8 h-8 text-walnut-800/20 group-hover:text-gold-500 transition"></i>
        </div>

        <!-- Alamat -->
        <div class="bg-cream-50 border border-walnut-800/10 p-6 flex items-center justify-between group hover:border-gold-500 transition">
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-muted font-bold">Alamat</p>
                <p class="text-3xl font-display font-black text-walnut-950 mt-1">{{ $addressesCount }}</p>
                <a href="{{ route('profile.show') }}" class="text-[0.65rem] text-gold-600 uppercase tracking-widest font-bold hover:text-walnut-950 block mt-2">Kelola &rarr;</a>
            </div>
            <i data-lucide="map-pin" class="w-8 h-8 text-walnut-800/20 group-hover:text-gold-500 transition"></i>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-cream-50 border border-walnut-800/10 p-6 flex items-center justify-between group hover:border-gold-500 transition">
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-muted font-bold">Pesanan</p>
                <p class="text-3xl font-display font-black text-walnut-950 mt-1">{{ $totalOrdersCount }}</p>
                <a href="{{ route('orders.history') }}" class="text-[0.65rem] text-gold-600 uppercase tracking-widest font-bold hover:text-walnut-950 block mt-2">Riwayat &rarr;</a>
            </div>
            <i data-lucide="shopping-bag" class="w-8 h-8 text-walnut-800/20 group-hover:text-gold-500 transition"></i>
        </div>

        <!-- Kupon -->
        <div class="bg-cream-50 border border-walnut-800/10 p-6 flex items-center justify-between group hover:border-gold-500 transition">
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-muted font-bold">Privilege</p>
                <p class="text-3xl font-display font-black text-walnut-950 mt-1">{{ $activeCoupons->count() }}</p>
                <a href="#coupons-section" class="text-[0.65rem] text-gold-600 uppercase tracking-widest font-bold hover:text-walnut-950 block mt-2">Kupon &rarr;</a>
            </div>
            <i data-lucide="ticket" class="w-8 h-8 text-walnut-800/20 group-hover:text-gold-500 transition"></i>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- Left Col: Orders & History -->
        <div class="lg:col-span-8 space-y-12">
            <!-- Order status summary cards -->
            <div class="space-y-6">
                <h3 class="text-[0.7rem] font-bold uppercase tracking-widest text-walnut-950 border-b border-walnut-800/10 pb-2">Status Pesanan Aktif</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="border border-walnut-800/20 p-5 text-center bg-transparent">
                        <span class="text-3xl font-display font-black text-walnut-950">{{ $pendingOrdersCount }}</span>
                        <span class="text-[0.6rem] block text-muted font-bold uppercase tracking-widest mt-2">Pending</span>
                    </div>
                    <div class="border border-walnut-800/20 p-5 text-center bg-transparent">
                        <span class="text-3xl font-display font-black text-walnut-950">{{ $paidOrdersCount + $processingOrdersCount }}</span>
                        <span class="text-[0.6rem] block text-muted font-bold uppercase tracking-widest mt-2">Diproses</span>
                    </div>
                    <div class="border border-walnut-800/20 p-5 text-center bg-transparent">
                        <span class="text-3xl font-display font-black text-walnut-950">{{ $shippedOrdersCount }}</span>
                        <span class="text-[0.6rem] block text-muted font-bold uppercase tracking-widest mt-2">Dikirim</span>
                    </div>
                    <div class="border border-gold-500/50 p-5 text-center bg-gold-50/30">
                        <span class="text-3xl font-display font-black text-gold-600">{{ $completedOrdersCount }}</span>
                        <span class="text-[0.6rem] block text-gold-600 font-bold uppercase tracking-widest mt-2">Selesai</span>
                    </div>
                </div>
            </div>

            <!-- Transaction History Table -->
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-walnut-800/10 pb-2">
                    <h3 class="text-[0.7rem] font-bold uppercase tracking-widest text-walnut-950">Riwayat Terkini</h3>
                    <a href="{{ route('orders.history') }}" class="text-[0.65rem] font-bold uppercase tracking-widest text-gold-600 hover:text-walnut-950 transition">Semua &rarr;</a>
                </div>

                @if($orders->isEmpty())
                    <div class="text-center py-16 space-y-4 bg-cream-50 border border-walnut-800/10">
                        <i data-lucide="inbox" class="w-8 h-8 text-walnut-800/20 mx-auto"></i>
                        <p class="text-[0.75rem] text-muted font-bold uppercase tracking-widest">Belum ada riwayat koleksi.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-walnut-800/20 text-[0.65rem] font-bold uppercase tracking-widest text-muted">
                                    <th class="pb-4">Referensi</th>
                                    <th class="pb-4">Tanggal</th>
                                    <th class="pb-4">Investasi</th>
                                    <th class="pb-4">Status</th>
                                    <th class="pb-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-50 transition">
                                        <td class="py-5 font-display text-[0.8rem] font-black uppercase tracking-tight text-walnut-950">#{{ $order->order_code }}</td>
                                        <td class="py-5 text-[0.75rem] text-muted font-medium">{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="py-5 text-[0.75rem] text-walnut-900 font-bold tracking-wider">IDR {{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td class="py-5">
                                            @if($order->status === 'pending')
                                                <span class="text-[0.6rem] font-bold uppercase tracking-widest text-walnut-500 border border-walnut-500 px-2 py-1">Pending</span>
                                            @elseif($order->status === 'paid' || $order->status === 'processing')
                                                <span class="text-[0.6rem] font-bold uppercase tracking-widest text-walnut-900 border border-walnut-900 px-2 py-1">Diproses</span>
                                            @elseif($order->status === 'shipped')
                                                <span class="text-[0.6rem] font-bold uppercase tracking-widest text-gold-600 border border-gold-500 px-2 py-1">Dikirim</span>
                                            @elseif($order->status === 'completed')
                                                <span class="text-[0.6rem] font-bold uppercase tracking-widest bg-walnut-900 text-gold-500 px-2 py-1 border border-walnut-900">Selesai</span>
                                            @else
                                                <span class="text-[0.6rem] font-bold uppercase tracking-widest text-red-600 border border-red-600 px-2 py-1">Batal</span>
                                            @endif
                                        </td>
                                        <td class="py-5 text-right space-x-3">
                                            <a href="{{ route('orders.show', $order->order_code) }}" class="text-[0.65rem] font-bold uppercase tracking-widest text-gold-600 hover:text-walnut-950 transition">Detail</a>
                                            
                                            @if($order->status === 'pending')
                                                <form action="{{ route('orders.pay', $order->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-[0.65rem] font-bold uppercase tracking-widest text-walnut-950 hover:text-gold-600 transition">Bayar</button>
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
        </div>

        <!-- Right Col: Last Order details & Coupons list -->
        <div class="lg:col-span-4 space-y-12">
            <!-- Last Order Status Card -->
            @if($lastOrder)
                <div class="space-y-4">
                    <h3 class="text-[0.7rem] font-bold uppercase tracking-widest text-walnut-950 border-b border-walnut-800/10 pb-2">Pesanan Terakhir</h3>
                    
                    <div class="bg-cream-50 border border-walnut-800/10 p-6 space-y-5">
                        <div class="flex items-center justify-between border-b border-walnut-800/10 pb-4">
                            <span class="text-[0.65rem] uppercase tracking-widest text-muted font-bold">Referensi</span>
                            <span class="font-display text-[0.8rem] font-black uppercase text-walnut-950">#{{ $lastOrder->order_code }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-walnut-800/10 pb-4">
                            <span class="text-[0.65rem] uppercase tracking-widest text-muted font-bold">Total</span>
                            <span class="text-[0.75rem] font-bold tracking-widest text-walnut-950">IDR {{ number_format($lastOrder->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-2">
                            <span class="text-[0.65rem] uppercase tracking-widest text-muted font-bold">Status</span>
                            <span>
                                @if($lastOrder->status === 'pending')
                                    <span class="text-[0.6rem] font-bold uppercase tracking-widest text-walnut-500">Pending</span>
                                @elseif($lastOrder->status === 'paid' || $lastOrder->status === 'processing')
                                    <span class="text-[0.6rem] font-bold uppercase tracking-widest text-walnut-900">Diproses</span>
                                @elseif($lastOrder->status === 'shipped')
                                    <span class="text-[0.6rem] font-bold uppercase tracking-widest text-gold-600">Dikirim</span>
                                @elseif($lastOrder->status === 'completed')
                                    <span class="text-[0.6rem] font-bold uppercase tracking-widest text-gold-600">Selesai</span>
                                @else
                                    <span class="text-[0.6rem] font-bold uppercase tracking-widest text-red-600">Batal</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="pt-4">
                            <a href="{{ route('orders.show', $lastOrder->order_code) }}" class="block w-full py-3 text-center border border-walnut-800/20 text-[0.65rem] uppercase tracking-widest font-bold text-walnut-900 hover:border-gold-500 hover:text-gold-600 transition">Lacak Pesanan</a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Coupons Section -->
            <div id="coupons-section" class="space-y-4">
                <h3 class="text-[0.7rem] font-bold uppercase tracking-widest text-walnut-950 border-b border-walnut-800/10 pb-2">Privilege Code</h3>

                @if($activeCoupons->isEmpty())
                    <div class="text-center py-8 bg-cream-50 border border-walnut-800/10">
                        <p class="text-[0.65rem] text-muted font-bold uppercase tracking-widest">Tidak ada kode tersedia.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($activeCoupons as $coupon)
                            <div class="border border-walnut-800/20 p-5 flex items-center justify-between bg-transparent">
                                <div class="space-y-1">
                                    <p class="text-[0.6rem] uppercase tracking-widest text-gold-600 font-bold">{{ $coupon->type === 'fixed' ? 'Potongan Langsung' : 'Diskon Spesial' }}</p>
                                    <p class="font-display text-lg font-black text-walnut-950 uppercase">
                                        @if($coupon->type === 'fixed')
                                            IDR {{ number_format($coupon->value, 0, ',', '.') }}
                                        @else
                                            {{ intval($coupon->value) }}% OFF
                                        @endif
                                    </p>
                                    @if($coupon->min_purchase > 0)
                                        <p class="text-[0.6rem] text-muted font-medium">Min. Pembelian IDR {{ number_format($coupon->min_purchase, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="block text-[0.65rem] font-bold uppercase tracking-widest bg-walnut-900 text-gold-500 px-3 py-1.5 mb-2">
                                        {{ $coupon->code }}
                                    </span>
                                    @if($coupon->end_date)
                                        <p class="text-[0.55rem] uppercase tracking-widest text-muted font-bold">Exp: {{ $coupon->end_date->format('d M y') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
