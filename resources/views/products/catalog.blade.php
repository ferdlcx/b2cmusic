@extends('layouts.app')

@section('title', 'Katalog - DjudasMS')

@section('content')
<div class="space-y-12 py-4">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-8 space-y-4">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Koleksi Eksklusif</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Katalog Kami.</h1>
    </div>

    <!-- Search & Brand Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-end gap-4 pb-4">
        <div class="w-full sm:max-w-md">
            <form action="{{ route('catalog') }}" method="GET" class="relative flex items-center gap-4">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('min_price'))
                    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                @endif
                @if(request('max_price'))
                    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                @endif

                <div class="relative flex-1">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        placeholder="Cari karya..." 
                        class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    <button type="submit" class="absolute right-0 top-3 text-walnut-800 hover:text-gold-600 transition">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="w-48">
                    <select name="brand" onchange="this.form.submit()" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium appearance-none cursor-pointer">
                        <option value="">Semua Merek</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->slug }}" {{ request('brand') === $b->slug ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-16 items-start">
        <!-- Sidebar Filters -->
        <aside class="space-y-12 sticky top-10">
            <!-- Categories -->
            <div class="space-y-4">
                <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-950 font-bold block">Kategori</span>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('catalog', array_merge(request()->except('category', 'page'))) }}" 
                        class="text-[0.7rem] uppercase tracking-wider font-bold transition flex items-center justify-between pb-2 border-b {{ !request('category') ? 'text-gold-600 border-gold-500' : 'text-walnut-800 hover:text-gold-500 border-transparent hover:border-walnut-800/20' }}">
                        <span>Semua Koleksi</span>
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('catalog', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" 
                            class="text-[0.7rem] uppercase tracking-wider font-bold transition flex items-center justify-between pb-2 border-b {{ request('category') === $cat->slug ? 'text-gold-600 border-gold-500' : 'text-walnut-800 hover:text-gold-500 border-transparent hover:border-walnut-800/20' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-[0.6rem] text-muted">{{ $cat->products()->count() }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Advanced Filters -->
            <form action="{{ route('catalog') }}" method="GET" class="space-y-8">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('brand'))
                    <input type="hidden" name="brand" value="{{ request('brand') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-950 font-bold block">Refine</span>
                        <a href="{{ route('catalog') }}" class="text-[0.6rem] uppercase tracking-widest text-gold-600 font-bold hover:text-walnut-950 transition">Reset</a>
                    </div>
                    
                    <!-- Harga -->
                    <div class="space-y-3">
                        <span class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-800">Harga (IDR)</span>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.7rem] font-medium">
                            <span class="text-walnut-400">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full bg-transparent border-b border-walnut-800/20 py-2 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.7rem] font-medium">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 bg-walnut-900 text-gold-500 text-[0.65rem] uppercase tracking-[0.2em] font-bold hover:bg-gold-600 hover:text-white transition duration-300">Terapkan</button>
                    </div>
                </div>
            </form>

            <!-- Sorting -->
            <div class="space-y-4 pt-6 border-t border-walnut-800/10">
                <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-950 font-bold block">Urutan</span>
                <div class="flex flex-col gap-3">
                    @foreach([
                        'newest' => 'Terbaru',
                        'price_asc' => 'Harga Terendah',
                        'price_desc' => 'Harga Tertinggi'
                    ] as $key => $label)
                        <a href="{{ route('catalog', array_merge(request()->except('page'), ['sort' => $key])) }}" 
                            class="text-[0.7rem] uppercase tracking-wider font-bold transition flex items-center justify-between {{ (request('sort', 'newest') === $key) ? 'text-gold-600' : 'text-walnut-800 hover:text-gold-500' }}">
                            <span>{{ $label }}</span>
                            @if(request('sort', 'newest') === $key)
                                <span class="w-1.5 h-1.5 bg-gold-500 rounded-full"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

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
                <div class="grid gap-12 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($products as $product)
                        <article class="group flex flex-col h-full">
                            <a href="{{ route('products.show', $product->slug) }}" class="block mb-6 relative overflow-hidden bg-cream-50 border border-walnut-800/5 h-[340px]">
                                <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $product->name }}" class="h-full w-full object-cover mix-blend-multiply opacity-90 transition duration-700 group-hover:opacity-100" />
                                
                                @if($product->stock == 0)
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
</div>
@endsection
