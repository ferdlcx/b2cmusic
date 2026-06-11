@extends('admin.layouts.admin')

@section('title', 'Detail Pengguna - Admin DjudasMS')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6 flex items-center justify-between gap-4">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-muted font-bold">Interaksi Sistem</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Detail Pengguna</h1>
        </div>
        <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-walnut-800/10 bg-cream-50 rounded-xl text-xs font-semibold uppercase tracking-wider text-walnut-800 hover:bg-cream-100 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2 text-slate-400"></i> Kembali
        </a>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Profile Info card -->
        <div class="space-y-6">
            <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-6 text-center shadow-sm space-y-4">
                <div class="w-20 h-20 bg-indigo-50 border-2 border-indigo-200 rounded-full flex items-center justify-center font-display font-black text-2xl text-indigo-700 uppercase mx-auto overflow-hidden">
                    @if($user->profile_photo)
                        <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                    @else
                        {{ substr($user->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <h3 class="font-display font-black text-lg text-slate-950 uppercase tracking-tight">{{ $user->name }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $user->email }}</p>
                </div>
                <div class="border-t border-slate-100 pt-4 space-y-3.5 text-left text-xs font-medium">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Telepon</span>
                        <span class="text-slate-800 font-semibold">{{ $user->phone ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Role</span>
                        <span class="text-slate-800 font-semibold uppercase tracking-wider text-[0.65rem]">{{ $user->role }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Status</span>
                        <span>
                            @php
                                $userStatus = strtolower($user->status);
                                $isActive = $userStatus === 'active' || $user->status == '1';
                            @endphp
                            @if($isActive)
                                <span class="text-[0.6rem] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-250 px-2 py-0.5 rounded-full">Aktif</span>
                            @else
                                <span class="text-[0.6rem] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded-full">Ditangguhkan</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Bergabung</span>
                        <span class="text-slate-800 font-semibold">{{ $user->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                @if($user->role !== 'admin' && $user->role !== 'super_admin')
                    <div class="pt-2 border-t border-slate-100">
                        <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-slate-950 text-white rounded-2xl font-bold uppercase text-[0.65rem] tracking-widest hover:bg-slate-850 transition">
                                {{ $isActive ? 'Tangguhkan Akses' : 'Aktifkan Akses' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Addresses & Orders -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Addresses -->
            <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-6 md:p-8 shadow-sm space-y-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-walnut-900 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-indigo-650"></i> Alamat Tersimpan ({{ $user->addresses->count() }})
                </h3>

                @if($user->addresses->isEmpty())
                    <p class="text-xs text-slate-400 font-semibold">Pengguna belum menambahkan alamat pengiriman.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($user->addresses as $address)
                            <div class="border border-walnut-800/10 rounded-2xl p-4.5 space-y-2.5 relative {{ $address->is_default ? 'bg-indigo-50/20 border-indigo-200' : '' }}">
                                <div class="flex items-center justify-between">
                                    <span class="font-display font-black text-[0.65rem] uppercase tracking-widest text-slate-800">{{ $address->label }}</span>
                                    @if($address->is_default)
                                        <span class="text-[0.55rem] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">Utama</span>
                                    @endif
                                </div>
                                <div class="text-xs font-semibold text-walnut-800">
                                    <strong>{{ $address->name }}</strong><br>
                                    {{ $address->phone }}
                                </div>
                                <p class="text-[0.7rem] text-muted leading-relaxed font-normal">
                                    {{ $address->address }}, {{ $address->village }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }}, {{ $address->postal_code }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Orders -->
            <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-6 md:p-8 shadow-sm space-y-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-walnut-900 flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-4 h-4 text-indigo-650"></i> Riwayat Pesanan ({{ $user->orders->count() }})
                </h3>

                @if($user->orders->isEmpty())
                    <p class="text-xs text-slate-400 font-semibold">Pengguna belum melakukan pembelian.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="pb-3 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400">Invoice</th>
                                    <th class="pb-3 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400">Tanggal</th>
                                    <th class="pb-3 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400">Total</th>
                                    <th class="pb-3 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400 text-center">Status</th>
                                    <th class="pb-3 text-[0.65rem] font-bold uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->orders as $order)
                                    <tr class="border-b border-slate-50 last:border-0 hover:bg-cream-100/50 transition">
                                        <td class="py-3.5 font-bold uppercase text-walnut-900 text-xs">#{{ $order->order_code }}</td>
                                        <td class="py-3.5 text-muted font-medium text-xs">{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="py-3.5 font-bold text-slate-800 text-xs">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td class="py-3.5 text-center">
                                            @if($order->status === 'pending')
                                                <span class="inline-block text-[0.55rem] font-bold uppercase tracking-wider bg-gold-50 text-gold-700 border border-gold-200 px-2 py-0.5 rounded">Pending</span>
                                            @elseif($order->status === 'paid' || $order->status === 'processing')
                                                <span class="inline-block text-[0.55rem] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded">Paid</span>
                                            @elseif($order->status === 'shipped')
                                                <span class="inline-block text-[0.55rem] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded">Shipped</span>
                                            @elseif($order->status === 'completed')
                                                <span class="inline-block text-[0.55rem] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded">Completed</span>
                                            @else
                                                <span class="inline-block text-[0.55rem] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded">Canceled</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 text-right">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-[0.7rem] font-black uppercase text-slate-800 hover:underline">Kelola</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
