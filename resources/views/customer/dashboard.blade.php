@extends('layouts.app')

@section('title', 'Dashboard Saya - DjudasMS')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-4xl font-black uppercase tracking-tight text-slate-950">Dashboard Saya</h1>
            <p class="text-sm text-slate-500 font-normal">Halo, <span class="font-bold text-slate-800">{{ auth()->user()->name }}</span>! Kelola pesanan, alamat, dan wishlist Anda di sini.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('returns.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-slate-200 bg-white rounded-2xl text-xs font-semibold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2 text-rose-400"></i> Riwayat Retur
            </a>
            <a href="{{ route('profile.show') }}" class="inline-flex items-center justify-center px-5 py-3 border border-slate-200 bg-white rounded-2xl text-xs font-semibold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition">
                <i data-lucide="user" class="w-4 h-4 mr-2 text-slate-400"></i> Edit Profil
            </a>
            <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center px-5 py-3 bg-indigo-600 rounded-2xl text-xs font-semibold uppercase tracking-wider text-white hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300">
                <i data-lucide="shopping-cart" class="w-4 h-4 mr-2"></i> Belanja
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Wishlist -->
        <div class="bg-white border border-slate-200/80 rounded-[28px] p-6 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 shrink-0">
                <i data-lucide="heart" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold">Wishlist</p>
                <p class="text-2xl font-black text-slate-950 mt-0.5">{{ $wishlistCount }} <span class="text-xs font-normal text-slate-500">item</span></p>
                <a href="{{ route('wishlist.index') }}" class="text-[0.7rem] text-indigo-600 font-bold hover:underline block mt-1">Lihat Wishlist &rarr;</a>
            </div>
        </div>

        <!-- Alamat -->
        <div class="bg-white border border-slate-200/80 rounded-[28px] p-6 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 shrink-0">
                <i data-lucide="map-pin" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold">Alamat Tersimpan</p>
                <p class="text-2xl font-black text-slate-950 mt-0.5">{{ $addressesCount }} <span class="text-xs font-normal text-slate-500">alamat</span></p>
                <a href="{{ route('profile.show') }}" class="text-[0.7rem] text-indigo-600 font-bold hover:underline block mt-1">Kelola Alamat &rarr;</a>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white border border-slate-200/80 rounded-[28px] p-6 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 shrink-0">
                <i data-lucide="wallet" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold">Total Transaksi</p>
                <p class="text-2xl font-black text-slate-950 mt-0.5">{{ $totalOrdersCount }} <span class="text-xs font-normal text-slate-500">pesanan</span></p>
                <a href="{{ route('orders.history') }}" class="text-[0.7rem] text-indigo-600 font-bold hover:underline block mt-1">Riwayat Pesanan &rarr;</a>
            </div>
        </div>

        <!-- Kupon -->
        <div class="bg-white border border-slate-200/80 rounded-[28px] p-6 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 shrink-0">
                <i data-lucide="ticket" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold">Kupon Aktif</p>
                <p class="text-2xl font-black text-slate-950 mt-0.5">{{ $activeCoupons->count() }} <span class="text-xs font-normal text-slate-500">tersedia</span></p>
                <a href="#coupons-section" class="text-[0.7rem] text-indigo-600 font-bold hover:underline block mt-1">Lihat Kupon &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Orders & History -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Order status summary cards -->
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 md:p-8 shadow-sm space-y-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <i data-lucide="activity" class="w-4 h-4 text-indigo-600"></i> Status Pesanan Anda
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-slate-50 rounded-2xl p-4 text-center border border-slate-100">
                        <span class="text-2xl font-black text-slate-950">{{ $pendingOrdersCount }}</span>
                        <span class="text-[0.65rem] block text-slate-500 font-bold uppercase tracking-wider mt-1">Belum Bayar</span>
                    </div>
                    <div class="bg-indigo-50/50 rounded-2xl p-4 text-center border border-indigo-100/50">
                        <span class="text-2xl font-black text-indigo-700">{{ $paidOrdersCount + $processingOrdersCount }}</span>
                        <span class="text-[0.65rem] block text-indigo-600 font-bold uppercase tracking-wider mt-1">Diproses</span>
                    </div>
                    <div class="bg-blue-50/50 rounded-2xl p-4 text-center border border-blue-100/50">
                        <span class="text-2xl font-black text-blue-700">{{ $shippedOrdersCount }}</span>
                        <span class="text-[0.65rem] block text-blue-600 font-bold uppercase tracking-wider mt-1">Dikirim</span>
                    </div>
                    <div class="bg-emerald-50/50 rounded-2xl p-4 text-center border border-emerald-100/50">
                        <span class="text-2xl font-black text-emerald-700">{{ $completedOrdersCount }}</span>
                        <span class="text-[0.65rem] block text-emerald-600 font-bold uppercase tracking-wider mt-1">Selesai</span>
                    </div>
                </div>
            </div>

            <!-- Transaction History Table -->
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 md:p-8 shadow-sm space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-indigo-600"></i> Riwayat Transaksi Terakhir
                    </h3>
                    <a href="{{ route('orders.history') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">Semua Pesanan &rarr;</a>
                </div>

                @if($orders->isEmpty())
                    <div class="text-center py-12 space-y-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <i data-lucide="shopping-bag" class="w-10 h-10 text-slate-300 mx-auto"></i>
                        <p class="text-xs text-slate-500 font-semibold">Anda belum memiliki riwayat transaksi.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="pb-4 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400">Kode</th>
                                    <th class="pb-4 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400">Tanggal</th>
                                    <th class="pb-4 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400">Total</th>
                                    <th class="pb-4 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400">Status</th>
                                    <th class="pb-4 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr class="border-b border-slate-50 last:border-b-0 hover:bg-slate-50/50 transition">
                                        <td class="py-4.5 font-display text-xs font-black uppercase text-slate-950">#{{ $order->order_code }}</td>
                                        <td class="py-4.5 text-xs text-slate-500 font-medium">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                        <td class="py-4.5 text-xs text-slate-800 font-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td class="py-4.5">
                                            @if($order->status === 'pending')
                                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 px-2 py-1 rounded-full">Belum Bayar</span>
                                            @elseif($order->status === 'paid' || $order->status === 'processing')
                                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-1 rounded-full">Diproses</span>
                                            @elseif($order->status === 'shipped')
                                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200 px-2 py-1 rounded-full">Dikirim</span>
                                            @elseif($order->status === 'completed')
                                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-1 rounded-full">Selesai</span>
                                            @else
                                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 px-2 py-1 rounded-full">Batal</span>
                                            @endif
                                        </td>
                                        <td class="py-4.5 text-right space-x-2">
                                            <a href="{{ route('orders.show', $order->order_code) }}" class="text-[0.7rem] font-bold text-indigo-600 hover:text-indigo-700">Detail</a>
                                            
                                            @if($order->status === 'pending')
                                                <form action="{{ route('orders.pay', $order->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-[0.7rem] font-black text-amber-600 hover:text-amber-700 uppercase">Bayar</button>
                                                </form>
                                            @endif

                                            @if($order->status === 'completed' || $order->status === 'shipped' || $order->status === 'paid')
                                                <a href="{{ route('orders.invoice', $order->order_code) }}" class="text-[0.7rem] font-bold text-slate-500 hover:text-slate-700">Invoice</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Col: Last Order details & Coupons list -->
        <div class="space-y-8">
            <!-- Last Order Status Card -->
            @if($lastOrder)
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 shadow-sm space-y-5">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <i data-lucide="package" class="w-4 h-4 text-indigo-600"></i> Pesanan Terakhir
                    </h3>
                    
                    <div class="bg-slate-50 border border-slate-200/50 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-semibold">Kode Pesanan</span>
                            <span class="font-display text-xs font-black text-slate-900">#{{ $lastOrder->order_code }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-semibold">Total Tagihan</span>
                            <span class="text-xs font-bold text-slate-900">Rp {{ number_format($lastOrder->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-semibold">Status</span>
                            <span>
                                @if($lastOrder->status === 'pending')
                                    <span class="text-[0.6rem] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full">Belum Bayar</span>
                                @elseif($lastOrder->status === 'paid' || $lastOrder->status === 'processing')
                                    <span class="text-[0.6rem] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded-full">Diproses</span>
                                @elseif($lastOrder->status === 'shipped')
                                    <span class="text-[0.6rem] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full">Dikirim</span>
                                @elseif($lastOrder->status === 'completed')
                                    <span class="text-[0.6rem] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full">Selesai</span>
                                @else
                                    <span class="text-[0.6rem] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded-full">Batal</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="border-t border-slate-200 pt-3 flex items-center justify-between">
                            <a href="{{ route('orders.show', $lastOrder->order_code) }}" class="text-[0.7rem] text-indigo-600 font-bold hover:underline">Detail Pesanan &rarr;</a>
                            
                            @if($lastOrder->status === 'completed')
                                @php
                                    $hasReturn = \App\Models\ReturnRequest::where('order_id', $lastOrder->id)->exists();
                                @endphp
                                @if(!$hasReturn)
                                    <a href="{{ route('returns.create', $lastOrder->id) }}" class="text-[0.7rem] text-rose-600 font-bold hover:underline">Ajukan Retur</a>
                                @else
                                    <span class="text-[0.6rem] text-slate-400 font-semibold">Retur Diajukan</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Coupons Section -->
            <div id="coupons-section" class="bg-white border border-slate-200/80 rounded-[32px] p-6 shadow-sm space-y-5">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <i data-lucide="ticket" class="w-4 h-4 text-indigo-600"></i> Kupon yang Anda Miliki
                </h3>

                @if($activeCoupons->isEmpty())
                    <div class="text-center py-8 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-xs text-slate-500 font-semibold">Saat ini tidak ada kupon yang tersedia.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($activeCoupons as $coupon)
                            <div class="relative bg-indigo-50 border border-dashed border-indigo-300 rounded-2xl p-4 flex items-center justify-between overflow-hidden">
                                <!-- Deco circles for coupon look -->
                                <div class="absolute -left-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-white border-r border-indigo-200 rounded-full"></div>
                                <div class="absolute -right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-white border-l border-indigo-200 rounded-full"></div>
                                
                                <div class="pl-2.5">
                                    <p class="text-[0.6rem] uppercase tracking-wider text-indigo-600 font-bold">{{ $coupon->type === 'fixed' ? 'Potongan Langsung' : 'Diskon' }}</p>
                                    <p class="text-sm font-black text-slate-950 mt-0.5">
                                        @if($coupon->type === 'fixed')
                                            Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                        @else
                                            {{ intval($coupon->value) }}% Off
                                        @endif
                                    </p>
                                    @if($coupon->min_purchase > 0)
                                        <p class="text-[0.65rem] text-slate-500 font-medium mt-1">Min. Belanja: Rp {{ number_format($coupon->min_purchase, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                <div class="text-right pr-2.5">
                                    <span class="inline-block text-[0.65rem] font-mono font-black uppercase bg-indigo-600 text-white rounded-lg px-2.5 py-1.5 shadow-sm border border-indigo-700/10">
                                        {{ $coupon->code }}
                                    </span>
                                    @if($coupon->end_date)
                                        <p class="text-[0.6rem] text-slate-400 font-semibold mt-1">Hingga: {{ $coupon->end_date->format('d M Y') }}</p>
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
