@extends('layouts.app')

@section('title', 'Retur Barang Saya - MusicStore Luxe')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-[0.65rem] uppercase tracking-[0.4em] text-rose-600 font-bold bg-rose-50 px-3.5 py-1.5 rounded-full inline-block">Pengembalian</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tight text-slate-950 mt-2">Retur Barang Saya</h1>
            <p class="text-sm text-slate-500 font-normal">Daftar pengajuan pengembalian barang dan dana Anda.</p>
        </div>
        <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-slate-200 bg-white rounded-2xl text-xs font-semibold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2 text-slate-400"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Returns List -->
    @if($returns->isEmpty())
        <div class="text-center py-20 bg-white border border-slate-200/80 rounded-[32px] shadow-sm space-y-4 max-w-2xl mx-auto">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mx-auto">
                <i data-lucide="package-x" class="w-7 h-7"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-display text-lg font-bold uppercase tracking-tight text-slate-950">Tidak Ada Pengembalian</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Anda tidak memiliki pengajuan retur barang yang sedang berjalan atau selesai.</p>
            </div>
            <div class="pt-2">
                <a href="{{ route('orders.history') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 rounded-2xl text-xs font-semibold uppercase tracking-wider text-white hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300">
                    Lihat Riwayat Pesanan
                </a>
            </div>
        </div>
    @else
        <div class="space-y-6">
            @foreach($returns as $return)
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 md:p-8 shadow-sm flex flex-col md:flex-row justify-between gap-6">
                    <!-- Details -->
                    <div class="space-y-4 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-display text-sm font-black uppercase text-slate-950">Pesanan #{{ $return->order->order_code }}</span>
                            <span class="text-xs text-slate-400 font-medium">&bull; Diajukan pada {{ $return->created_at->format('d M Y, H:i') }}</span>
                            
                            <!-- Status Badges -->
                            <div>
                                @if($return->status === 'pending')
                                    <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full">Menunggu Persetujuan</span>
                                @elseif($return->status === 'approved')
                                    <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full">Disetujui</span>
                                @else
                                    <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded-full">Ditolak</span>
                                @endif
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="space-y-1">
                            <p class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold">Alasan Pengembalian</p>
                            <p class="text-xs text-slate-700 leading-relaxed font-medium">{{ $return->reason }}</p>
                        </div>

                        <!-- Admin Notes (If any) -->
                        @if($return->admin_notes)
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4.5 text-xs space-y-1">
                                <span class="font-bold text-slate-700 block uppercase tracking-wider text-[0.6rem]">Catatan Admin:</span>
                                <p class="text-slate-600 font-medium leading-relaxed">{{ $return->admin_notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Photo Attachment -->
                    @if($return->photo)
                        <div class="w-full md:w-36 lg:w-44 shrink-0 aspect-video md:aspect-square bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 relative group">
                            <img src="{{ Storage::url($return->photo) }}" alt="Foto Bukti" class="w-full h-full object-cover" />
                            <a href="{{ Storage::url($return->photo) }}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[0.65rem] font-bold uppercase tracking-wider transition">
                                <i data-lucide="maximize-2" class="w-4 h-4 mr-1"></i> Perbesar
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
