@extends('admin.layouts.admin')

@section('title', 'Moderasi Retur Barang - Admin DjudasMS')

@section('admin_content')
<div class="space-y-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-walnut-800/10">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Pusat Resolusi</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Moderasi Retur.</h1>
            <p class="text-[0.7rem] font-bold text-muted uppercase tracking-widest">Tinjau & verifikasi pengajuan pengembalian barang dari pelanggan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-500 hover:text-walnut-950 transition">← Dashboard</a>
        </div>
    </div>

    <!-- Table / List -->
    @if($returns->isEmpty())
        <div class="bg-cream-50 border border-walnut-800/10 p-16 text-center text-muted font-medium">
            Belum ada pengajuan retur yang masuk ke sistem.
        </div>
    @else
        <div class="space-y-8">
            @foreach($returns as $return)
                <div class="bg-cream-50 border border-walnut-800/10 p-6 md:p-8 flex flex-col md:flex-row justify-between gap-8">
                    <!-- details -->
                    <div class="space-y-6 flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-4 border-b border-walnut-800/10 pb-4">
                            <span class="font-display text-lg font-black text-walnut-950 uppercase tracking-tight">Pesanan #{{ $return->order->order_code }}</span>
                            <span class="text-[0.65rem] text-muted font-bold uppercase tracking-widest">&bull; Diajukan oleh {{ $return->user->name }} ({{ $return->user->email }})</span>
                            <span class="text-[0.65rem] text-muted font-bold uppercase tracking-widest">&bull; {{ $return->created_at->format('d M Y, H:i') }}</span>
                        </div>

                        <!-- Status badge -->
                        <div class="flex items-center gap-4">
                            <span class="text-[0.65rem] text-walnut-500 font-bold uppercase tracking-widest">Status:</span>
                            @if($return->status === 'pending')
                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-widest bg-walnut-200 text-walnut-800 border border-walnut-300 px-3 py-1">Menunggu Moderasi</span>
                            @elseif($return->status === 'approved')
                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-widest bg-gold-100 text-gold-800 border border-gold-300 px-3 py-1">Disetujui</span>
                            @else
                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-widest bg-red-100 text-red-800 border border-red-200 px-3 py-1">Ditolak</span>
                            @endif
                        </div>

                        <!-- Reason -->
                        <div class="space-y-2">
                            <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-500 font-bold block">Alasan Pengembalian</span>
                            <p class="text-sm text-walnut-900 font-medium leading-relaxed">{{ $return->reason }}</p>
                        </div>

                        <!-- Moderation Form / Admin notes -->
                        @if($return->status === 'pending')
                            <div class="bg-cream-100 border border-walnut-800/10 p-6 space-y-6">
                                <span class="font-bold text-walnut-900 block uppercase tracking-widest text-[0.65rem] border-b border-walnut-800/10 pb-2">Tindakan Moderasi</span>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Approve Form -->
                                    <form action="{{ route('admin.returns.approve', $return->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div class="space-y-2">
                                            <label class="text-[0.6rem] uppercase tracking-widest text-walnut-600 font-bold block">Catatan Persetujuan (Opsional)</label>
                                            <input type="text" name="admin_notes" class="w-full px-4 py-2.5 bg-white border border-walnut-800/20 text-walnut-900 text-[0.75rem] font-medium focus:outline-none focus:border-gold-500 transition" placeholder="Instruksi pengiriman ke gudang..." />
                                        </div>
                                        <button type="submit" class="w-full py-3 bg-walnut-900 hover:bg-gold-600 text-gold-500 hover:text-white font-bold text-[0.65rem] uppercase tracking-[0.2em] transition duration-300">Setujui Retur</button>
                                    </form>

                                    <!-- Reject Form -->
                                    <form action="{{ route('admin.returns.reject', $return->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div class="space-y-2">
                                            <label class="text-[0.6rem] uppercase tracking-widest text-red-600 font-bold block">Alasan Penolakan (Wajib)</label>
                                            <input type="text" name="admin_notes" required class="w-full px-4 py-2.5 bg-white border border-red-500/30 text-walnut-900 text-[0.75rem] font-medium focus:outline-none focus:border-red-500 transition" placeholder="Alasan menolak retur..." />
                                        </div>
                                        <button type="submit" class="w-full py-3 border border-red-600 hover:bg-red-600 text-red-600 hover:text-white font-bold text-[0.65rem] uppercase tracking-[0.2em] transition duration-300">Tolak Retur</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            @if($return->admin_notes)
                                <div class="bg-cream-100 border border-walnut-800/10 p-5 space-y-2">
                                    <span class="font-bold text-walnut-900 block uppercase tracking-widest text-[0.65rem]">Catatan Admin:</span>
                                    <p class="text-sm text-walnut-700 font-medium leading-relaxed">{{ $return->admin_notes }}</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Media Attachments -->
                    <div class="flex flex-col gap-4 w-full md:w-56 shrink-0">
                        <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-500 font-bold block border-b border-walnut-800/10 pb-2">Bukti Lampiran</span>
                        
                        @if($return->photo)
                            <div class="w-full aspect-square bg-cream-100 border border-walnut-800/10 relative group">
                                <img src="{{ Storage::url($return->photo) }}" alt="Bukti Foto" class="w-full h-full object-cover mix-blend-multiply" />
                                <a href="{{ Storage::url($return->photo) }}" target="_blank" class="absolute inset-0 bg-walnut-900/80 opacity-0 group-hover:opacity-100 flex items-center justify-center text-gold-500 text-[0.65rem] font-bold uppercase tracking-widest transition duration-300">
                                    <i data-lucide="maximize-2" class="w-4 h-4 mr-2"></i> Perbesar Foto
                                </a>
                            </div>
                        @endif

                        @if($return->video)
                            <a href="{{ Storage::url($return->video) }}" target="_blank" class="w-full py-3 bg-walnut-900 hover:bg-gold-600 text-gold-500 hover:text-white text-center text-[0.65rem] uppercase tracking-[0.2em] font-bold transition duration-300 flex items-center justify-center">
                                <i data-lucide="video" class="w-4 h-4 mr-2"></i> Lihat Video Unboxing
                            </a>
                        @else
                            <div class="w-full py-3 border border-red-500/30 bg-red-50 text-center text-[0.6rem] uppercase tracking-widest font-bold text-red-600">
                                <i data-lucide="alert-circle" class="w-3 h-3 inline mr-1"></i> Tidak Ada Video
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="pt-8">
                {{ $returns->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
