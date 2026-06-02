@extends('layouts.app')

@section('title', 'Katalog Produk - MusicStore Luxe')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-200/60 pb-8 space-y-3">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Katalog Belanja</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tight text-slate-950">Jelajahi Produk Kami</h1>
        <p class="text-slate-500 text-sm max-w-2xl font-normal">Temukan berbagai instrumen musik kelas dunia, piringan hitam pilihan, dan perlengkapan studio murni di satu tempat.</p>
    </div>

    <!-- Layout Grid -->
    <div class="grid gap-10 lg:grid-cols-[260px_1fr]">
        <!-- Sidebar Filters -->
        <aside class="space-y-8">
            <!-- Search -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
                <span class="text-xs uppercase tracking-widest text-slate-900 font-black block">Pencarian</span>
                <form action="{{ route('catalog') }}" method="GET" class="relative">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        placeholder="Cari produk..." 
                        class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white text-xs transition duration-300 font-medium" />
                    <button type="submit" class="absolute right-3 top-3 text-slate-400 hover:text-indigo-600 transition">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <!-- Categories -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
                <span class="text-xs uppercase tracking-widest text-slate-900 font-black block">Kategori</span>
                <div class="flex flex-col gap-1.5">
                    <a href="{{ route('catalog', array_merge(request()->except('category', 'page'))) }}" 
                        class="text-xs py-2.5 px-3.5 rounded-xl transition font-semibold flex items-center justify-between {{ !request('category') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>Semua Kategori</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('catalog', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" 
                            class="text-xs py-2.5 px-3.5 rounded-xl transition font-semibold flex items-center justify-between {{ request('category') === $cat->slug ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-[0.65rem] {{ request('category') === $cat->slug ? 'text-white/80' : 'text-slate-400' }}">({{ $cat->products()->count() }})</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Sorting -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
                <span class="text-xs uppercase tracking-widest text-slate-900 font-black block">Urutkan</span>
                <div class="flex flex-col gap-1.5">
                    @foreach([
                        'newest' => 'Terbaru',
                        'price_asc' => 'Harga Terendah',
                        'price_desc' => 'Harga Tertinggi'
                    ] as $key => $label)
                        <a href="{{ route('catalog', array_merge(request()->except('page'), ['sort' => $key])) }}" 
                            class="text-xs py-2.5 px-3.5 rounded-xl transition font-semibold flex items-center justify-between {{ (request('sort', 'newest') === $key) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span>{{ $label }}</span>
                            <i data-lucide="{{ request('sort', 'newest') === $key ? 'check' : 'chevron-right' }}" class="w-3.5 h-3.5"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="space-y-10">
            @if($products->isEmpty())
                <div class="bg-white border border-slate-200 rounded-[32px] p-16 text-center shadow-sm space-y-4">
                    <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                        <i data-lucide="package-search" class="w-8 h-8"></i>
                    </div>
                    <div class="space-y-1">
                        <p class="font-display text-lg font-bold text-slate-900 uppercase">Produk Tidak Ditemukan</p>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">Silakan coba kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white text-xs font-semibold tracking-wider rounded-xl hover:bg-indigo-700 transition duration-300">
                            Reset Pencarian
                        </a>
                    </div>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($products as $product)
                        <article class="group overflow-hidden rounded-[32px] bg-white border border-slate-200/80 shadow-sm transition hover:shadow-xl hover:-translate-y-1.5 duration-300 flex flex-col justify-between h-full">
                            <a href="{{ route('products.show', $product->slug) }}" class="block">
                                <div class="h-60 overflow-hidden bg-slate-50 flex items-center justify-center relative">
                                    <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-103" />
                                    
                                    @if($product->stock == 0)
                                        <div class="absolute inset-0 bg-white/80 backdrop-blur-[2px] flex items-center justify-center">
                                            <span class="text-[0.65rem] uppercase tracking-widest font-black bg-rose-600 text-white px-4 py-2.5 rounded-xl shadow-md">Stok Habis</span>
                                        </div>
                                    @else
                                        <!-- Brand Badge -->
                                        <div class="absolute top-4 left-4">
                                            <span class="text-[0.65rem] uppercase tracking-wider font-bold bg-white text-slate-900 shadow-sm px-3 py-1.5 rounded-xl">
                                                {{ $product->brand }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-6 space-y-3">
                                    <span class="text-[0.65rem] uppercase tracking-[0.25em] text-slate-400 font-bold block">{{ $product->category->name }}</span>
                                    <h3 class="font-display text-base font-bold uppercase tracking-tight text-slate-950 leading-snug line-clamp-2 h-12 group-hover:text-indigo-600 transition">{{ $product->name }}</h3>
                                    
                                    <!-- Rating -->
                                    <div class="flex items-center gap-1 text-amber-500 text-xs">
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-500"></i>
                                        <span class="font-bold text-slate-700">4.9</span>
                                        <span class="text-slate-400">(Ulasan)</span>
                                    </div>
                                    
                                    <p class="text-lg font-black text-indigo-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                            </a>
                            <div class="px-6 pb-6">
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-xs uppercase tracking-widest text-slate-900 font-bold hover:text-indigo-600 transition">Detail</a>
                                    @if($product->stock > 0)
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="inline-flex items-center gap-1.5 text-xs uppercase tracking-wider bg-indigo-600 text-white px-3.5 py-2 rounded-xl hover:bg-indigo-700 transition duration-300 font-semibold shadow-sm hover:shadow-indigo-600/10">
                                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i> Beli
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="pt-6 border-t border-slate-200/60">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
