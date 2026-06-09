@extends('layouts.app')

@section('title', 'Retur Barang Saya - DjudasMS')

@section('content')
<div class="space-y-8 py-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-walnut-800/10">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Pusat Resolusi</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Retur Barang.</h1>
            <p class="text-sm text-muted font-medium">Lacak status pengajuan pengembalian barang dan dana Anda.</p>
        </div>
        <a href="{{ route('customer.dashboard') }}" class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-500 hover:text-walnut-950 transition flex items-center">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Returns List -->
    @if($returns->isEmpty())
        <div class="py-24 text-center space-y-6 bg-cream-50 border border-walnut-800/10">
            <i data-lucide="package-x" class="w-12 h-12 text-walnut-800/20 mx-auto"></i>
            <div class="space-y-2">
                <p class="font-display text-2xl font-black text-walnut-950 uppercase tracking-tighter">Tidak Ada Pengembalian</p>
                <p class="text-sm text-muted max-w-sm mx-auto leading-relaxed">Anda belum memiliki pengajuan retur barang yang sedang berjalan.</p>
            </div>
            <a href="{{ route('orders.history') }}" class="inline-block px-8 py-3 bg-walnut-900 text-gold-500 text-[0.65rem] uppercase tracking-[0.2em] font-bold hover:bg-gold-600 hover:text-white transition duration-300">
                Lihat Riwayat Pesanan
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($returns as $return)
                <div class="bg-cream-50 border border-walnut-800/10 p-6 md:p-8 flex flex-col md:flex-row justify-between gap-8">
                    <!-- Details -->
                    <div class="space-y-6 flex-1">
                        <div class="flex flex-wrap items-center gap-4 border-b border-walnut-800/10 pb-4">
                            <span class="font-display text-lg font-black uppercase text-walnut-950 tracking-tight">Pesanan #{{ $return->order->order_code }}</span>
                            <span class="text-[0.65rem] text-muted font-bold uppercase tracking-widest">&bull; {{ $return->created_at->format('d M Y, H:i') }}</span>
                            
                            <!-- Status Badges -->
                            <div>
                                @if($return->status === 'pending')
                                    <span class="inline-block text-[0.6rem] font-bold uppercase tracking-widest bg-walnut-200 text-walnut-800 border border-walnut-300 px-3 py-1">Menunggu Persetujuan</span>
                                @elseif($return->status === 'approved')
                                    <span class="inline-block text-[0.6rem] font-bold uppercase tracking-widest bg-gold-100 text-gold-800 border border-gold-300 px-3 py-1">Disetujui</span>
                                @else
                                    <span class="inline-block text-[0.6rem] font-bold uppercase tracking-widest bg-red-100 text-red-800 border border-red-200 px-3 py-1">Ditolak</span>
                                @endif
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="space-y-2">
                            <p class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-500 font-bold">Alasan Pengembalian</p>
                            <p class="text-sm text-walnut-900 leading-relaxed font-medium">{{ $return->reason }}</p>
                        </div>

                        <!-- Admin Notes (If any) -->
                        @if($return->admin_notes)
                            <div class="bg-cream-100 border border-walnut-800/10 p-5 space-y-2">
                                <span class="font-bold text-walnut-900 block uppercase tracking-widest text-[0.65rem]">Catatan Admin:</span>
                                <p class="text-walnut-700 font-medium leading-relaxed text-sm">{{ $return->admin_notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Media Attachments -->
                    <div class="flex flex-col gap-4 w-full md:w-48 shrink-0">
                        @if($return->photo)
                            <div class="w-full aspect-square bg-cream-100 border border-walnut-800/10 relative group">
                                <img src="{{ Storage::url($return->photo) }}" alt="Foto Bukti" class="w-full h-full object-cover mix-blend-multiply" />
                                <a href="{{ Storage::url($return->photo) }}" target="_blank" class="absolute inset-0 bg-walnut-900/80 opacity-0 group-hover:opacity-100 flex items-center justify-center text-gold-500 text-[0.65rem] font-bold uppercase tracking-widest transition duration-300">
                                    <i data-lucide="maximize-2" class="w-4 h-4 mr-2"></i> Lihat Foto
                                </a>
                            </div>
                        @endif
                        @if($return->video)
                            <a href="{{ Storage::url($return->video) }}" target="_blank" class="w-full py-3 border border-walnut-800/20 text-center text-[0.65rem] uppercase tracking-widest font-bold text-walnut-800 hover:bg-walnut-900 hover:text-gold-500 transition flex items-center justify-center">
                                <i data-lucide="video" class="w-4 h-4 mr-2"></i> Putar Video
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
