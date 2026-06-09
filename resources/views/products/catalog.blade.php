@extends('layouts.app')

@section('title', 'Katalog - DjudasMS')

@section('content')
@php
    $wishlistProductIds = [];
    $wishlistItemsMapping = [];
    if(auth()->check() && auth()->user()->wishlist) {
        $wishlistItems = auth()->user()->wishlist->items;
        foreach($wishlistItems as $item) {
            $wishlistProductIds[] = $item->product_id;
            $wishlistItemsMapping[$item->product_id] = $item->id;
        }
    }
@endphp
<div class="space-y-12 py-8">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-12 space-y-4 text-center">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">The Collection</span>
        <h1 class="font-display text-4xl md:text-6xl font-black uppercase tracking-tighter text-walnut-950">Jelajahi <span class="text-gold-500">Katalog.</span></h1>
        <p class="text-muted text-sm md:text-base max-w-2xl mx-auto font-medium leading-relaxed">Pilihan instrumen musik kelas dunia, piringan hitam vintage, dan perangkat studio murni dengan spesifikasi editorial.</p>
    </div>

    <!-- Horizontal Filter Bar -->
    <div class="space-y-8" x-data="{ showFilters: false }">
        <!-- Top Control Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-walnut-800/10 pb-6">
            <!-- Search -->
            <form action="{{ route('catalog') }}" method="GET" class="relative w-full md:max-w-xs">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari karya..." 
                    class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                <button type="submit" class="absolute right-0 top-3 text-walnut-800 hover:text-gold-600 transition">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="flex items-center gap-6 overflow-x-auto scrollbar-hide pb-2 md:pb-0">
                <!-- Categories as Horizontal Links -->
                <a href="{{ route('catalog', array_merge(request()->except('category', 'page'))) }}" 
                    class="whitespace-nowrap text-[0.7rem] uppercase tracking-wider font-bold transition border-b-2 pb-1 {{ !request('category') ? 'text-gold-600 border-gold-500' : 'text-walnut-800 hover:text-gold-500 border-transparent' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('catalog', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" 
                        class="whitespace-nowrap text-[0.7rem] uppercase tracking-wider font-bold transition border-b-2 pb-1 {{ request('category') === $cat->slug ? 'text-gold-600 border-gold-500' : 'text-walnut-800 hover:text-gold-500 border-transparent' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
                
                <span class="w-px h-4 bg-walnut-800/20"></span>
                
                <button type="button" @click="showFilters = !showFilters" class="flex items-center gap-2 whitespace-nowrap text-[0.7rem] uppercase tracking-wider font-bold transition border-b-2 pb-1 text-walnut-800 hover:text-gold-500 border-transparent">
                    <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                    <span x-text="showFilters ? 'Tutup Filter' : 'Filter Lanjutan'"></span>
                </button>
            </div>
        </div>

        <!-- Expandable Advanced Filters (Brands, Price, Sort) -->
        <div x-show="showFilters" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 -translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             class="bg-cream-50 border border-walnut-800/10 p-8 space-y-8"
             style="display: none;">
            
            <form action="{{ route('catalog') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-12">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <!-- Sorting -->
                <div class="space-y-4">
                    <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-950 font-bold block border-b border-walnut-800/10 pb-2">Urutan</span>
                    <div class="flex flex-col gap-2">
                        @foreach([
                            'newest' => 'Terbaru',
                            'price_asc' => 'Harga Terendah',
                            'price_desc' => 'Harga Tertinggi'
                        ] as $key => $label)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="sort" value="{{ $key }}" class="w-3.5 h-3.5 text-gold-600 bg-cream-50 border-walnut-800/30 focus:ring-gold-500" {{ request('sort', 'newest') === $key ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-[0.7rem] uppercase tracking-wider text-walnut-800 group-hover:text-gold-600 font-bold transition">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div class="space-y-4">
                    <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-950 font-bold block border-b border-walnut-800/10 pb-2">Harga (IDR)</span>
                    <div class="flex flex-col gap-4">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Minimum" class="w-full bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.7rem] font-medium">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Maksimum" class="w-full bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.7rem] font-medium">
                    </div>
                </div>

                <!-- Brands -->
                @if(isset($brands) && $brands->count() > 0)
                <div class="space-y-4">
                    <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-950 font-bold block border-b border-walnut-800/10 pb-2">Merek</span>
                    <div class="flex flex-wrap gap-3 max-h-40 overflow-y-auto pr-2 scrollbar-hide">
                        @foreach($brands as $brand)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="brand[]" value="{{ $brand->id }}" class="w-3.5 h-3.5 text-gold-600 bg-cream-50 border-walnut-800/30 rounded-none focus:ring-gold-500" {{ in_array($brand->id, (array)request('brand', [])) ? 'checked' : '' }}>
                            <span class="text-[0.7rem] uppercase tracking-wider text-walnut-800 group-hover:text-gold-600 font-bold transition">{{ $brand->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="md:col-span-3 flex items-center justify-end gap-4 pt-4 border-t border-walnut-800/10">
                    <a href="{{ route('catalog') }}" class="text-[0.65rem] uppercase tracking-[0.2em] font-bold text-walnut-500 hover:text-walnut-950 transition">Reset</a>
                    <button type="submit" class="px-8 py-3 bg-walnut-900 text-gold-500 text-[0.65rem] uppercase tracking-[0.2em] font-bold hover:bg-gold-600 hover:text-white transition duration-300">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="space-y-12">
        @if($products->isEmpty())
            <div class="py-24 text-center space-y-6">
                <i data-lucide="inbox" class="w-12 h-12 text-walnut-800/20 mx-auto"></i>
                <div class="space-y-2">
                    <p class="font-display text-2xl font-black text-walnut-950 uppercase tracking-tighter">Koleksi Tidak Ditemukan</p>
                    <p class="text-sm text-muted max-w-sm mx-auto leading-relaxed">Kriteria yang Anda cari belum tersedia dalam kurasi kami.</p>
                </div>
                <a href="{{ route('catalog') }}" class="inline-block px-8 py-3 bg-walnut-900 text-gold-500 text-[0.65rem] uppercase tracking-[0.2em] font-bold hover:bg-gold-600 hover:text-white transition duration-300">
                    Kembali ke Katalog
                </a>
            </div>
        @else
            <div class="grid gap-12 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach($products as $product)
                    <article class="group flex flex-col h-full">
                        <a href="{{ route('products.show', $product->slug) }}" class="block mb-6 relative overflow-hidden bg-cream-50 border border-walnut-800/5 h-[340px]">
                            <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $product->name }}" class="h-full w-full object-cover mix-blend-multiply opacity-90 transition duration-700 group-hover:scale-105 group-hover:opacity-100" />
                            
                            @if($product->stock == 0)
                                <div class="absolute inset-0 bg-cream-100/60 backdrop-blur-sm flex items-center justify-center">
                                    <span class="text-[0.65rem] uppercase tracking-[0.3em] font-black text-walnut-900 border border-walnut-900 px-4 py-2">Terjual</span>
                                </div>
                            @else
                                <div class="absolute top-4 left-4">
                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] font-bold bg-walnut-900 text-cream-50 px-3 py-1.5">
                                        {{ $product->brand ?: 'Unbranded' }}
                                    </span>
                                </div>
                            @endif
                        </a>
                        
                        <div class="space-y-3 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[0.6rem] uppercase tracking-[0.2em] text-gold-600 font-bold">{{ $product->category->name }}</span>
                                <h3 class="font-display text-lg font-bold uppercase tracking-tight text-walnut-950 leading-snug mt-1 group-hover:text-gold-600 transition">{{ $product->name }}</h3>
                            </div>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-walnut-800/10 mt-auto">
                                <p class="text-sm font-bold tracking-widest text-walnut-900">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                                
                                <div class="flex items-center gap-4">
                                    @php
                                        $inWishlist = in_array($product->id, $wishlistProductIds);
                                        $wishlistItemId = $inWishlist ? $wishlistItemsMapping[$product->id] : null;
                                    @endphp

                                    @if($inWishlist)
                                        <form action="{{ route('wishlist.destroy', $wishlistItemId) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gold-600 hover:text-red-600 transition" title="Hapus dari Wishlist">
                                                <i data-lucide="heart" class="w-4 h-4 fill-gold-600"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('wishlist.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" class="text-walnut-400 hover:text-gold-600 transition" title="Tambah ke Wishlist">
                                                <i data-lucide="heart" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($product->stock > 0)
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="text-walnut-800 hover:text-gold-600 transition" title="Beli">
                                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination Links -->
            <div class="pt-12 border-t border-walnut-800/10">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
