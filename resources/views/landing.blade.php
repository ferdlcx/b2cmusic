@extends('layouts.app')

@section('title', 'MusicStore Luxe - Premium Instruments & Records')

@section('content')
<!-- Hero Section -->
<section class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] items-center py-8">
    <div class="space-y-8">
        <div class="inline-flex items-center gap-3 text-[0.75rem] uppercase tracking-[0.45em] text-slate-500">
            <span>ARTISAN TOKO MUSIK</span>
            <span class="h-px w-16 bg-slate-900"></span>
        </div>
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-6xl lg:text-[4.8rem] leading-tight font-black uppercase tracking-[-0.05em] text-slate-950">Suara Murni.<br />Craftsmanship<br />Ikonik.</h1>
        </div>
        <p class="max-w-2xl text-lg md:text-xl text-slate-600 leading-relaxed">Selamat datang di MusicStore Luxe. Kami menyediakan instrumen musik artisan, piringan hitam pilihan, dan gear rekaman kelas dunia untuk menyempurnakan ekspresi seni Anda.</p>
        <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center px-8 py-4 bg-slate-950 text-white uppercase text-sm tracking-[0.18em] hover:bg-slate-800 transition">Jelajahi Toko</a>
            <a href="#collections" class="inline-flex items-center justify-center px-8 py-4 border border-slate-300 text-slate-700 uppercase text-sm tracking-[0.18em] hover:border-slate-950 hover:text-slate-950 transition">Kategori</a>
        </div>
    </div>

    <!-- Right Graphic -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
        <div class="overflow-hidden rounded-[40px] bg-slate-100 shadow-[0_30px_80px_rgba(15,23,42,0.08)] h-[350px]">
            <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1200&q=80" alt="Luxury guitar" class="h-full w-full object-cover" />
        </div>
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="overflow-hidden rounded-[32px] bg-slate-100 shadow-[0_20px_50px_rgba(15,23,42,0.08)] h-[220px]">
                <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1200&q=80" alt="Studio headphones" class="h-full w-full object-cover" />
            </div>
            <div class="rounded-[32px] bg-slate-950 p-8 flex flex-col justify-between text-white min-h-[220px]">
                <div>
                    <span class="text-xs uppercase tracking-[0.35em] text-slate-400">Signature Drop</span>
                    <h2 class="mt-3 text-2xl font-black uppercase tracking-[-0.04em]">Premium Vinyl</h2>
                </div>
                <p class="text-xs leading-relaxed text-slate-300">Piringan hitam orisinal dari musisi-musisi legendaris untuk audio analog otentik.</p>
            </div>
        </div>
    </div>
</section>

<!-- Collections Section -->
<section id="collections" class="py-16 mt-16">
    <div class="space-y-10">
        <div>
            <span class="text-[0.75rem] uppercase tracking-[0.45em] text-slate-500">Curated Collections</span>
            <h2 class="text-3xl md:text-4xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Koleksi Musik Pilihan</h2>
            <p class="mt-3 text-slate-600 max-w-xl">Dari piringan hitam legendaris hingga gitar akustik premium, setiap barang kurasi kami memiliki resonansi luar biasa.</p>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($categories as $cat)
                <a href="{{ route('catalog', ['category' => $cat->slug]) }}" class="group overflow-hidden rounded-[28px] border border-slate-200 bg-white p-8 shadow-[0_20px_45px_rgba(15,23,42,0.06)] transition hover:-translate-y-1 hover:border-slate-950 flex flex-col justify-between h-[200px]">
                    <div class="text-2xl font-black uppercase tracking-[0.05em] text-slate-950 leading-tight">{{ $cat->name }}</div>
                    <div class="mt-8 text-xs uppercase tracking-[0.4em] text-slate-500 opacity-80 group-hover:opacity-100 group-hover:text-slate-950 transition flex items-center justify-between">
                        <span>Lihat Koleksi</span>
                        <span>→</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="products" class="py-16 bg-slate-50 rounded-[40px] border border-slate-200/60 p-8 lg:p-12 shadow-[0_30px_80px_rgba(15,23,42,0.03)] my-16">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-12">
        <div>
            <span class="text-[0.75rem] uppercase tracking-[0.45em] text-slate-500">Featured Instruments</span>
            <h2 class="text-4xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Instrumen Unggulan</h2>
        </div>
        <p class="max-w-md text-slate-600">Pilihan gear musik terbaik yang siap menemani Anda tampil di panggung, rekaman di studio, atau menjadi koleksi estetis di rumah.</p>
    </div>
    
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($products as $product)
            <article class="group overflow-hidden rounded-[32px] bg-white border border-slate-200 shadow-[0_25px_70px_rgba(15,23,42,0.08)] transition hover:-translate-y-1 flex flex-col justify-between">
                <a href="{{ route('products.show', $product->slug) }}" class="block">
                    <div class="h-72 overflow-hidden bg-slate-100 flex items-center justify-center">
                        <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                    </div>
                    <div class="p-8">
                        <span class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ $product->category->name }}</span>
                        <h3 class="mt-4 text-xl font-black uppercase tracking-[-0.04em] text-slate-950 leading-tight line-clamp-2 h-14">{{ $product->name }}</h3>
                        <p class="mt-3 text-lg font-semibold text-slate-700">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </a>
                <div class="px-8 pb-8">
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('products.show', $product->slug) }}" class="text-xs uppercase tracking-[0.3em] text-slate-900 font-bold hover:text-slate-600 transition">Detail Produk</a>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="text-xs uppercase tracking-[0.2em] bg-slate-950 text-white px-4 py-2 rounded-xl hover:bg-slate-800 transition">Beli</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-3 text-center py-12 text-slate-500">Belum ada produk unggulan yang tersedia.</div>
        @endforelse
    </div>
    
    <div class="text-center mt-12">
        <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center px-8 py-4 border border-slate-950 text-slate-950 uppercase text-xs tracking-[0.25em] hover:bg-slate-950 hover:text-white transition font-black">Lihat Semua Produk</a>
    </div>
</section>

<!-- Editorial Story Section -->
<section class="py-16 grid gap-12 lg:grid-cols-2 lg:items-center">
    <div class="space-y-6">
        <p class="text-xs uppercase tracking-[0.45em] text-slate-500">Kualitas Suara Maksimal</p>
        <h2 class="text-4xl font-black uppercase tracking-[-0.04em] text-slate-950">Desain Suara & Estetika Tanpa Kompromi.</h2>
        <p class="text-lg text-slate-600 leading-relaxed">MusicStore Luxe bukan sekadar toko retail. Kami adalah kurator seni musik. Kami percaya bahwa instrumen yang indah secara visual akan melahirkan melodi yang indah secara emosional.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-[0_20px_50px_rgba(15,23,42,0.04)] space-y-4">
            <div class="text-4xl font-black text-slate-950">01</div>
            <div class="text-xs uppercase tracking-[0.35em] text-slate-500 font-bold">Produk Terkurasi</div>
            <p class="text-sm text-slate-500">Setiap gitar dan piringan hitam diuji kualitas bunyinya oleh ahli audio kami sebelum masuk daftar display.</p>
        </div>
        <div class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-[0_20px_50px_rgba(15,23,42,0.04)] space-y-4">
            <div class="text-4xl font-black text-slate-950">02</div>
            <div class="text-xs uppercase tracking-[0.35em] text-slate-500 font-bold">Garansi Resmi</div>
            <p class="text-sm text-slate-500">Kami menjamin orisinalitas semua produk premium dengan opsi pengembalian dana penuh jika cacat.</p>
        </div>
    </div>
</section>
@endsection
