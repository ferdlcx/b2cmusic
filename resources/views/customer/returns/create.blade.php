@extends('layouts.app')

@section('title', 'Ajukan Pengembalian - DjudasMS')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between gap-4">
        <div>
            <span class="text-[0.65rem] uppercase tracking-[0.4em] text-rose-600 font-bold bg-rose-50 px-3.5 py-1.5 rounded-full inline-block">Formulir Retur</span>
            <h1 class="font-display text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Ajukan Pengembalian</h1>
            <p class="text-sm text-slate-500 font-normal">Pengajuan pengembalian barang untuk Pesanan <span class="font-bold text-slate-800">#{{ $order->order_code }}</span></p>
        </div>
        <a href="{{ route('orders.history') }}" class="inline-flex items-center justify-center p-2.5 border border-slate-200 bg-white rounded-2xl text-slate-500 hover:text-slate-850 hover:bg-slate-50 transition" title="Kembali ke Riwayat Pesanan">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
    </div>

    <!-- Order Items Summary Card -->
    <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 shadow-sm space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ringkasan Barang Pesanan</h3>
        <div class="space-y-3">
            @foreach($order->items as $item)
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-xl overflow-hidden shrink-0">
                        @if($item->product && $item->product->primaryImage)
                            <img src="{{ $item->product->primaryImage->image }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover" />
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i data-lucide="image" class="w-6 h-6"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-800 uppercase line-clamp-1">{{ $item->product ? $item->product->name : 'Produk Tidak Ditemukan' }}</p>
                        <p class="text-[0.7rem] text-slate-400 font-semibold">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="border-t border-slate-100 pt-3 flex justify-between items-center text-xs font-bold text-slate-800">
            <span>Total Transaksi</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Return Form -->
    <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm">
        <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <input type="hidden" name="order_id" value="{{ $order->id }}" />

            <!-- Reason -->
            <div class="space-y-1.5">
                <label for="reason" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alasan Pengembalian</label>
                <textarea name="reason" id="reason" rows="5" required
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold leading-relaxed @error('reason') border-rose-500 @enderror"
                    placeholder="Jelaskan alasan detail pengembalian produk Anda (misal: barang rusak saat diterima, produk salah dikirim, dll.)"></textarea>
                @error('reason')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Photo Upload -->
            <div class="space-y-1.5">
                <label for="photo" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Foto Bukti (Wajib)</label>
                <div class="relative">
                    <input type="file" name="photo" id="photo" required accept="image/*"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('photo') border-rose-500 @enderror" />
                </div>
                <p class="text-[0.65rem] text-slate-400 font-medium">Unggah foto produk yang menunjukkan kerusakan atau ketidaksesuaian (Max. 2MB, format JPEG/PNG).</p>
                @error('photo')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i> Kirim Pengajuan Retur
            </button>
        </form>
    </div>
</div>
@endsection
