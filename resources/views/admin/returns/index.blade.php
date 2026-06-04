@extends('admin.layouts.admin')

@section('title', 'Moderasi Retur Barang - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6">
        <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Transaksi</span>
        <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Moderasi Retur Barang</h1>
        <p class="text-xs text-slate-500">Kelola pengajuan retur barang dan pengembalian dana dari pelanggan.</p>
    </div>

    <!-- Table / List -->
    @if($returns->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-12 text-center text-slate-500">
            <i data-lucide="refresh-cw" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
            <p class="text-xs font-semibold">Belum ada pengajuan retur masuk.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($returns as $return)
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 md:p-8 shadow-sm flex flex-col md:flex-row justify-between gap-8">
                    <!-- details -->
                    <div class="space-y-5 flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-display text-sm font-black text-slate-950 uppercase">Pesanan #{{ $return->order->order_code }}</span>
                            <span class="text-xs text-slate-400 font-semibold">&bull; Diajukan oleh {{ $return->user->name }} ({{ $return->user->email }})</span>
                            <span class="text-xs text-slate-400 font-semibold">&bull; {{ $return->created_at->format('d M Y, H:i') }}</span>
                        </div>

                        <!-- Status badge -->
                        <div>
                            @if($return->status === 'pending')
                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full">Menunggu Moderasi</span>
                            @elseif($return->status === 'approved')
                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full">Disetujui</span>
                            @else
                                <span class="inline-block text-[0.6rem] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded-full">Ditolak</span>
                            @endif
                        </div>

                        <!-- Reason -->
                        <div class="space-y-1">
                            <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alasan Pengembalian</span>
                            <p class="text-xs text-slate-700 font-medium leading-relaxed">{{ $return->reason }}</p>
                        </div>

                        <!-- Moderation Form / Admin notes -->
                        @if($return->status === 'pending')
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 space-y-4">
                                <span class="font-bold text-slate-700 block uppercase tracking-wider text-[0.6rem]">Form Tindakan Moderasi</span>
                                
                                <div class="space-y-4">
                                    <!-- Approve Form -->
                                    <form action="{{ route('admin.returns.approve', $return->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="space-y-1.5">
                                            <label class="text-[0.6rem] uppercase tracking-wider text-slate-400 font-bold block">Catatan Admin / Catatan Persetujuan (Opsional)</label>
                                            <input type="text" name="admin_notes" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-650" placeholder="Misal: Retur disetujui, harap kirim barang ke gudang utama." />
                                        </div>
                                        <button type="submit" class="py-2.5 px-4.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-[0.65rem] uppercase tracking-widest transition">Setujui Pengembalian</button>
                                    </form>

                                    <div class="border-t border-slate-200 pt-3"></div>

                                    <!-- Reject Form -->
                                    <form action="{{ route('admin.returns.reject', $return->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="space-y-1.5">
                                            <label class="text-[0.6rem] uppercase tracking-wider text-slate-400 font-bold block">Alasan Penolakan (Wajib)</label>
                                            <input type="text" name="admin_notes" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-650" placeholder="Misal: Retur ditolak karena tidak ada kesalahan barang / kesalahan pengguna." />
                                        </div>
                                        <button type="submit" class="py-2.5 px-4.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-[0.65rem] uppercase tracking-widest transition">Tolak Pengembalian</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            @if($return->admin_notes)
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4.5 text-xs space-y-1">
                                    <span class="font-bold text-slate-700 block uppercase tracking-wider text-[0.6rem]">Catatan Admin:</span>
                                    <p class="text-slate-600 font-medium leading-relaxed">{{ $return->admin_notes }}</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Photo Proof -->
                    @if($return->photo)
                        <div class="w-full md:w-36 lg:w-44 shrink-0 aspect-video md:aspect-square bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 relative group">
                            <img src="{{ Storage::url($return->photo) }}" alt="Bukti Foto" class="w-full h-full object-cover" />
                            <a href="{{ Storage::url($return->photo) }}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[0.65rem] font-bold uppercase tracking-wider transition">
                                <i data-lucide="maximize-2" class="w-4 h-4 mr-1"></i> Perbesar
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="pt-4">
                {{ $returns->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
