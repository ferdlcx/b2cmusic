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
<div class="space-y-12 py-4">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-8 space-y-4">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Koleksi Eksklusif</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Katalog Kami.</h1>
    </div>

    <!-- Filter Toolbar -->
    <form action="{{ route('catalog') }}" method="GET" class="bg-cream-50 border border-walnut-800/10 p-4 md:p-6 flex flex-col xl:flex-row gap-6 items-end xl:items-center justify-between">
        <!-- Search -->
        <div class="w-full xl:w-64 relative">
            <label class="block text-[0.6rem] uppercase tracking-widest font-bold text-walnut-800 mb-2">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Cari karya..." 
                class="w-full bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
            <button type="submit" class="absolute right-0 bottom-2 text-walnut-800 hover:text-gold-600 transition">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="flex flex-col md:flex-row gap-6 w-full xl:w-auto">
            <!-- Category Dropdown -->
            <div class="flex-1 md:w-48">
                <label class="block text-[0.6rem] uppercase tracking-widest font-bold text-walnut-800 mb-2">Kategori</label>
                <select name="category" onchange="this.form.submit()" class="w-full bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium appearance-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Brand Dropdown -->
            <div class="flex-1 md:w-48">
                <label class="block text-[0.6rem] uppercase tracking-widest font-bold text-walnut-800 mb-2">Merek</label>
                <select name="brand" onchange="this.form.submit()" class="w-full bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium appearance-none cursor-pointer">
                    <option value="">Semua Merek</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ request('brand') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Price Filters -->
            <div class="flex-1 md:w-auto">
                <label class="block text-[0.6rem] uppercase tracking-widest font-bold text-walnut-800 mb-2">Harga (IDR)</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-24 bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.7rem] font-medium">
                    <span class="text-walnut-400">-</span>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-24 bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.7rem] font-medium">
                </div>
            </div>

            <!-- Sort Dropdown -->
            <div class="flex-1 md:w-48">
                <label class="block text-[0.6rem] uppercase tracking-widest font-bold text-walnut-800 mb-2">Urutan</label>
                <select name="sort" onchange="this.form.submit()" class="w-full bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium appearance-none cursor-pointer">
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <a href="{{ route('catalog') }}" class="py-2.5 px-4 text-[0.6rem] uppercase tracking-widest font-bold text-walnut-500 hover:text-gold-600 transition">Reset</a>
                <button type="submit" class="py-2.5 px-6 bg-walnut-900 text-gold-500 text-[0.65rem] uppercase tracking-widest font-bold hover:bg-gold-600 hover:text-white transition">Filter</button>
            </div>
        </div>
    </form>

    <!-- Product Grid -->
    <div>
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
            <!-- Changed grid layout to be full width without sidebar, supporting 2-4 columns based on screen width -->
            <div class="grid gap-x-8 gap-y-12 grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $product)
                    <article class="group flex flex-col h-full">
                        <!-- Swapped fixed height for aspect-square for a perfectly proportional box -->
                        <a href="{{ route('products.show', $product->slug) }}" class="block mb-4 relative overflow-hidden bg-cream-50 border border-walnut-800/5 aspect-square">
                            <!-- Removed hover:scale to keep it static, kept object-cover -->
                            <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $product->name }}" class="h-full w-full object-cover mix-blend-multiply opacity-90 transition duration-700 group-hover:opacity-100" />
                            
                            @if($product->stock <= 0)
                                <div class="absolute inset-0 bg-cream-100/60 backdrop-blur-sm flex items-center justify-center">
                                    <span class="text-[0.65rem] uppercase tracking-[0.3em] font-black text-walnut-900 border border-walnut-900 px-4 py-2">Terjual</span>
                                </div>
                            @else
                                <div class="absolute top-4 left-4">
                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] font-bold bg-walnut-900 text-cream-50 px-3 py-1.5">
                                        {{ optional($product->brand)->name ?? 'Tanpa Merek' }}
                                    </span>
                                </div>
                            @endif
                        </a>
                        
                        <div class="space-y-2 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[0.6rem] uppercase tracking-[0.2em] text-gold-600 font-bold">{{ $product->category->name }}</span>
                                <h3 class="font-display text-[0.95rem] font-bold uppercase tracking-tight text-walnut-950 leading-snug mt-1 group-hover:text-gold-600 transition line-clamp-2">{{ $product->name }}</h3>
                            </div>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-walnut-800/10 mt-auto">
                                <p class="text-xs font-bold tracking-widest text-walnut-900">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                                
                                <div class="flex items-center gap-3">
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
            <div class="pt-12">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
