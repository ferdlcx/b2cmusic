@extends('layouts.app')

@section('title', 'Katalog Produk - MusicStore Luxe')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-8 space-y-4">
        <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Katalog Belanja</span>
        <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-slate-950">Jelajahi Produk Kami</h1>
        <p class="text-slate-500 text-sm max-w-2xl">Temukan berbagai instrumen musik artisan, piringan hitam pilihan, dan perlengkapan studio murni di satu tempat.</p>
    </div>

    <!-- Layout Grid -->
    <div class="grid gap-10 lg:grid-cols-[250px_1fr]">
        <!-- Sidebar Filters -->
        <aside class="space-y-8">
            <!-- Search -->
            <form action="{{ route('catalog') }}" method="GET" class="space-y-3">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                <label for="search" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        placeholder="Cari produk..." 
                        class="w-full pl-4 pr-10 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 text-sm" />
                    <button type="submit" class="absolute right-3 top-3 text-slate-400 hover:text-slate-900">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Categories -->
            <div class="space-y-3">
                <span class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Kategori</span>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('catalog', array_merge(request()->except('category', 'page'))) }}" 
                        class="text-sm py-1.5 px-3 rounded-lg transition {{ !request('category') ? 'bg-slate-950 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                        Semua Kategori
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('catalog', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" 
                            class="text-sm py-1.5 px-3 rounded-lg transition flex items-center justify-between {{ request('category') === $cat->slug ? 'bg-slate-950 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-xs opacity-60">({{ $cat->products()->count() }})</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Sorting -->
            <div class="space-y-3">
                <span class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Urutkan</span>
                <div class="flex flex-col gap-2">
                    @foreach([
                        'newest' => 'Terbaru',
                        'price_asc' => 'Harga Terendah',
                        'price_desc' => 'Harga Tertinggi'
                    ] as $key => $label)
                        <a href="{{ route('catalog', array_merge(request()->except('page'), ['sort' => $key])) }}" 
                            class="text-sm py-1.5 px-3 rounded-lg transition {{ (request('sort', 'newest') === $key) ? 'bg-slate-950 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="space-y-10">
            @if($products->isEmpty())
                <div class="bg-white border border-slate-200 rounded-[32px] p-16 text-center text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mx-auto mb-4 text-slate-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.008 1.24l.885 1.77a2.25 2.25 0 002.007 1.24h1.98a2.25 2.25 0 002.007-1.24l.885-1.77a2.25 2.25 0 012.007-1.24h3.86m-18 0h18m-18 0l-1.085-5.426A2.25 2.25 0 015.224 5.25h13.552a2.25 2.25 0 012.188 2.824L19.75 13.5m-16.5 0a2.25 2.25 0 002.25 2.25h13.5a2.25 2.25 0 002.25-2.25m-16.5 0V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75V13.5" />
                    </svg>
                    <p class="text-lg font-bold text-slate-900">Produk Tidak Ditemukan</p>
                    <p class="text-sm mt-1 text-slate-400">Silakan coba kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($products as $product)
                        <article class="group overflow-hidden rounded-[32px] bg-white border border-slate-200 shadow-[0_25px_70px_rgba(15,23,42,0.04)] transition hover:-translate-y-1 flex flex-col justify-between">
                            <a href="{{ route('products.show', $product->slug) }}" class="block">
                                <div class="h-64 overflow-hidden bg-slate-50 flex items-center justify-center relative">
                                    <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                    @if($product->stock == 0)
                                        <div class="absolute inset-0 bg-white/80 backdrop-blur-[2px] flex items-center justify-center">
                                            <span class="text-xs uppercase tracking-widest font-black bg-rose-600 text-white px-4 py-2 rounded-xl">Habis Terjual</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-6">
                                    <span class="text-[0.65rem] uppercase tracking-[0.25em] text-slate-500 font-bold">{{ $product->category->name }}</span>
                                    <h3 class="mt-3 text-lg font-black uppercase tracking-tight text-slate-950 leading-snug line-clamp-2 h-12">{{ $product->name }}</h3>
                                    <p class="mt-2 text-sm text-slate-400 font-medium">Merek: {{ $product->brand ?: '-' }}</p>
                                    <p class="mt-3 text-base font-black text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                            </a>
                            <div class="px-6 pb-6">
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-xs uppercase tracking-[0.25em] text-slate-900 font-bold hover:text-slate-600 transition">Detail</a>
                                    @if($product->stock > 0)
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="text-xs uppercase tracking-[0.2em] bg-slate-950 text-white px-3.5 py-1.5 rounded-xl hover:bg-slate-800 transition">Beli</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="pt-6 border-t border-slate-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
