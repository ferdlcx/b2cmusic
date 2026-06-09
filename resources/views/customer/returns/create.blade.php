@extends('layouts.app')

@section('title', 'Ajukan Pengembalian - DjudasMS')

@section('content')
<div class="max-w-2xl mx-auto space-y-8 py-4">
    <!-- Header -->
    <div class="flex items-end justify-between gap-4 pb-6 border-b border-walnut-800/10">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Pusat Resolusi</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Formulir Retur.</h1>
            <p class="text-sm text-muted font-medium">Pengajuan pengembalian barang untuk Pesanan <span class="font-bold text-walnut-900">#{{ $order->order_code }}</span></p>
            @php
                $daysRemaining = max(0, 30 - $order->updated_at->diffInDays(now()));
            @endphp
            <div class="mt-2 inline-flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-gold-500 animate-pulse"></span>
                <span class="text-[0.65rem] text-gold-700 font-bold uppercase tracking-widest">Sisa Garansi: {{ $daysRemaining }} Hari lagi</span>
            </div>
        </div>
        <a href="{{ route('orders.history') }}" class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-500 hover:text-walnut-950 transition flex items-center">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
    </div>

    <!-- Order Items Summary Card -->
    <div class="bg-cream-50 border border-walnut-800/10 p-6 space-y-4">
        <h3 class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-walnut-500">Ringkasan Barang</h3>
        <div class="space-y-4">
            @foreach($order->items as $item)
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-cream-100 border border-walnut-800/10 shrink-0 p-1">
                        @if($item->product && $item->product->primaryImage)
                            <img src="{{ $item->product->primaryImage->image }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover mix-blend-multiply" />
                        @else
                            <div class="w-full h-full flex items-center justify-center text-walnut-300">
                                <i data-lucide="image" class="w-6 h-6"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 space-y-1">
                        <p class="font-display text-[0.8rem] font-black uppercase tracking-tight text-walnut-950 line-clamp-1">{{ $item->product ? $item->product->name : 'Produk Tidak Ditemukan' }}</p>
                        <p class="text-[0.7rem] text-muted font-bold tracking-widest">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="border-t border-walnut-800/10 pt-4 flex justify-between items-center text-[0.75rem] font-bold text-walnut-900 tracking-widest uppercase">
            <span>Total Transaksi</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Return Form -->
    <div class="bg-cream-50 border border-walnut-800/10 p-8">
        <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <input type="hidden" name="order_id" value="{{ $order->id }}" />

            <!-- Reason -->
            <div class="space-y-2">
                <label for="reason" class="text-[0.65rem] uppercase tracking-widest text-walnut-900 font-bold block">Alasan Pengembalian (Wajib)</label>
                <textarea name="reason" id="reason" rows="5" required
                    class="w-full px-4 py-3 bg-white border border-walnut-800/20 text-walnut-900 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium placeholder-walnut-400 @error('reason') border-red-500 @enderror"
                    placeholder="Jelaskan alasan detail pengembalian produk Anda (misal: cacat pabrik, kerusakan fungsi, dll.)"></textarea>
                @error('reason')
                    <span class="text-[0.65rem] text-red-600 font-bold block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Photo Upload -->
            <div class="space-y-2">
                <label for="photo" class="text-[0.65rem] uppercase tracking-widest text-walnut-900 font-bold block">Foto Fisik Barang (Wajib)</label>
                <input type="file" name="photo" id="photo" required accept="image/*"
                    class="w-full px-4 py-3 bg-white border border-walnut-800/20 text-walnut-900 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium @error('photo') border-red-500 @enderror" />
                <p class="text-[0.6rem] text-muted font-bold tracking-widest uppercase">Max 2MB, format JPEG/PNG.</p>
                @error('photo')
                    <span class="text-[0.65rem] text-red-600 font-bold block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Video Unboxing Upload -->
            <div class="space-y-2">
                <label for="video" class="text-[0.65rem] uppercase tracking-widest text-walnut-900 font-bold block">Video Unboxing Tanpa Jeda (Wajib)</label>
                <input type="file" name="video" id="video" required accept="video/*"
                    class="w-full px-4 py-3 bg-white border border-walnut-800/20 text-walnut-900 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium @error('video') border-red-500 @enderror" />
                <p class="text-[0.6rem] text-muted font-bold tracking-widest uppercase">Max 50MB, format MP4/MOV/AVI. Pastikan nomor seri produk terlihat jelas.</p>
                @error('video')
                    <span class="text-[0.65rem] text-red-600 font-bold block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Warning Box -->
            <div class="bg-red-50 border border-red-200 p-5 flex items-start gap-4">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
                <div class="space-y-1 text-red-900">
                    <p class="text-[0.7rem] font-bold uppercase tracking-widest">Ketentuan Retur</p>
                    <p class="text-[0.75rem] leading-relaxed font-medium">Pengajuan retur Anda akan ditinjau oleh Admin maksimal 2x24 jam. Jika video unboxing tidak sah atau ada indikasi pemalsuan, pengajuan otomatis ditolak. Biaya ongkos kirim pengembalian ditanggung pembeli.</p>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-walnut-800/10 flex justify-end">
                <button type="submit" class="px-8 py-3.5 bg-walnut-900 text-gold-500 text-[0.65rem] uppercase tracking-[0.2em] font-bold hover:bg-gold-600 hover:text-white transition duration-300">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
