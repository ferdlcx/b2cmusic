@extends('layouts.app')

@section('title', $product->name . ' - DjudasMS')

@section('content')
@php
    $inWishlist = false;
    $wishlistItemId = null;
    if(auth()->check() && auth()->user()->wishlist) {
        $wishlistItem = auth()->user()->wishlist->items->where('product_id', $product->id)->first();
        if($wishlistItem) {
            $inWishlist = true;
            $wishlistItemId = $wishlistItem->id;
        }
    }
@endphp
<div class="space-y-24 py-8">
    <!-- Breadcrumb -->
    <nav class="text-[0.65rem] font-bold uppercase tracking-widest text-muted flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-gold-600 flex items-center gap-1 transition">
            Home
        </a>
        <span class="text-walnut-800/30">/</span>
        <a href="{{ route('catalog') }}" class="hover:text-gold-600 transition">Shop</a>
        <span class="text-walnut-800/30">/</span>
        <a href="{{ route('catalog', ['category' => $product->category->slug]) }}" class="hover:text-gold-600 transition">{{ $product->category->name }}</a>
        <span class="text-walnut-800/30">/</span>
        <span class="text-walnut-950 font-bold truncate max-w-[200px]">{{ $product->name }}</span>
    </nav>

    <!-- Product Intro Block -->
    <div x-data="{ 
        activeImage: '{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}' 
    }" class="grid gap-16 lg:grid-cols-12 items-start">
        
        <!-- Left: Images Gallery & Video -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Main Image Frame with zoom-on-hover -->
            <div class="bg-cream-50 flex items-center justify-center min-h-[450px] md:min-h-[600px] border border-walnut-800/5">
                <img :src="activeImage" alt="{{ $product->name }}" class="w-full max-h-[700px] object-contain mix-blend-multiply transition-transform duration-700 hover:scale-105" />
            </div>

            <!-- Gallery Images (Alpine switcher) -->
            @if($product->images->count() > 0)
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                    <!-- Primary Image Thumbnail -->
                    @if($product->primaryImage)
                        <div @click="activeImage = '{{ $product->primaryImage->image }}'" 
                             :class="activeImage === '{{ $product->primaryImage->image }}' ? 'border-gold-500 opacity-100' : 'border-transparent opacity-60 hover:opacity-100'"
                             class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 bg-cream-50 border-b-2 cursor-pointer transition duration-300">
                            <img src="{{ $product->primaryImage->image }}" alt="Thumbnail" class="w-full h-full object-cover mix-blend-multiply" />
                        </div>
                    @endif
                    <!-- Secondary Images -->
                    @foreach($product->images->where('is_primary', false) as $img)
                        <div @click="activeImage = '{{ $img->image }}'" 
                             :class="activeImage === '{{ $img->image }}' ? 'border-gold-500 opacity-100' : 'border-transparent opacity-60 hover:opacity-100'"
                             class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 bg-cream-50 border-b-2 cursor-pointer transition duration-300">
                            <img src="{{ $img->image }}" alt="Thumbnail" class="w-full h-full object-cover mix-blend-multiply" />
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Embed Video Demonstration -->
            @if($product->videos->isNotEmpty())
                <div class="space-y-4 pt-12">
                    <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-950 font-bold block">Video Demonstration</span>
                    <div class="relative bg-walnut-950 aspect-video border border-walnut-800/10">
                        <iframe class="absolute inset-0 w-full h-full" src="{{ $product->videos->first()->video_url }}" title="{{ $product->videos->first()->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Information & Buy -->
        <div class="lg:col-span-5 space-y-10 lg:sticky lg:top-32">
            <div class="space-y-4 border-b border-walnut-800/10 pb-10">
                <span class="text-[0.65rem] uppercase tracking-[0.4em] text-gold-600 font-bold block">{{ $product->category->name }}</span>
                <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950 leading-[0.9]">{{ $product->name }}</h1>
                
                <div class="flex items-center gap-4 pt-4">
                    <span class="text-[0.7rem] uppercase tracking-widest text-muted font-bold">Brand: <strong class="text-walnut-900">{{ $product->brand ?: 'Unbranded' }}</strong></span>
                    <span class="h-3 w-px bg-walnut-800/20"></span>
                    <span class="text-[0.7rem] uppercase tracking-widest text-muted font-bold">SKU: <strong class="text-walnut-900">{{ $product->sku }}</strong></span>
                </div>
            </div>

            <!-- Price & Buy Section -->
            <div class="space-y-8">
                <div class="space-y-2">
                    <span class="text-[0.6rem] uppercase tracking-[0.2em] text-muted font-bold block">Investasi</span>
                    <span class="text-4xl font-display font-black text-walnut-950 tracking-tight">IDR {{ number_format($product->price, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center gap-4">
                    @if($product->stock > 0)
                        <span class="inline-flex items-center gap-2 text-[0.65rem] font-bold uppercase tracking-widest text-walnut-900">
                            <span class="h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse"></span>
                            Tersedia ({{ $product->stock }})
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 text-[0.65rem] font-bold uppercase tracking-widest text-red-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>
                            Terjual Habis
                        </span>
                    @endif
                </div>

                <!-- Add to Cart Form -->
                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-4 pt-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div x-data="{ qty: 1, maxQty: {{ $product->stock }} }" class="space-y-4">
                            <div class="flex items-center border border-walnut-800/20 w-32">
                                <button type="button" @click="if (qty > 1) qty--" class="w-10 py-3 text-walnut-500 hover:text-gold-600 font-bold transition">-</button>
                                <input type="number" name="quantity" x-model="qty" readonly class="flex-1 bg-transparent text-center text-[0.8rem] font-bold text-walnut-950 focus:outline-none" />
                                <button type="button" @click="if (qty < maxQty) qty++" class="w-10 py-3 text-walnut-500 hover:text-gold-600 font-bold transition">+</button>
                            </div>
                            
                            <button type="submit" 
                                class="w-full py-4 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.7rem] tracking-[0.2em] hover:bg-gold-600 hover:text-white transition duration-500 flex items-center justify-center gap-3">
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </form>
                @else
                    <button disabled class="w-full py-4 bg-cream-100 text-muted border border-walnut-800/10 font-bold uppercase text-[0.7rem] tracking-[0.2em] cursor-not-allowed">
                        Stok Tidak Tersedia
                    </button>
                @endif

                <!-- Add to Wishlist Form -->
                @if($inWishlist)
                    <form action="{{ route('wishlist.destroy', $wishlistItemId) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="w-full py-3.5 bg-transparent border-b border-gold-500 text-gold-600 font-bold uppercase text-[0.65rem] tracking-[0.2em] hover:border-red-600 hover:text-red-600 transition duration-300 flex items-center justify-center gap-2">
                            <i data-lucide="heart" class="w-3.5 h-3.5 fill-gold-600"></i> Hapus dari Wishlist
                        </button>
                    </form>
                @else
                    <form action="{{ route('wishlist.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" 
                            class="w-full py-3.5 bg-transparent border-b border-walnut-800/20 text-walnut-900 font-bold uppercase text-[0.65rem] tracking-[0.2em] hover:border-gold-500 hover:text-gold-600 transition duration-300 flex items-center justify-center gap-2">
                            <i data-lucide="heart" class="w-3.5 h-3.5"></i> Simpan ke Wishlist
                        </button>
                    </form>
                @endif
            </div>

            <!-- Short Description -->
            <div class="space-y-4 pt-8">
                <span class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-950 font-bold block">Ikhtisar</span>
                <p class="text-muted leading-relaxed text-[0.85rem] font-medium">{{ $product->short_description }}</p>
            </div>
        </div>
    </div>

    <!-- Details Tab & Review Grid -->
    <div class="grid gap-16 lg:grid-cols-2 pt-16 border-t border-walnut-800/10">
        <!-- Specifications & Description -->
        <div class="space-y-12">
            <div>
                <h2 class="font-display text-3xl font-black uppercase tracking-tighter text-walnut-950 mb-8">Spesifikasi</h2>
                @if($product->specifications->isEmpty())
                    <p class="text-[0.8rem] text-muted">Spesifikasi teknis tidak tersedia.</p>
                @else
                    <div class="border-t border-walnut-800/10">
                        <table class="w-full text-[0.75rem]">
                            <tbody>
                                @foreach($product->specifications as $spec)
                                    <tr class="border-b border-walnut-800/10">
                                        <td class="py-4 font-bold text-muted uppercase tracking-wider w-1/3">{{ $spec->spec_name }}</td>
                                        <td class="py-4 text-walnut-950 font-medium">{{ $spec->spec_value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div>
                <h3 class="font-display text-3xl font-black uppercase tracking-tighter text-walnut-950 mb-6">Detail</h3>
                <div class="prose prose-sm prose-slate max-w-none text-muted font-medium leading-relaxed">
                    {!! $product->description !!}
                </div>
            </div>
        </div>

        <!-- Reviews -->
        <div class="space-y-10">
            <div class="flex items-center justify-between border-b border-walnut-800/10 pb-6">
                <h2 class="font-display text-3xl font-black uppercase tracking-tighter text-walnut-950">Ulasan</h2>
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-display font-black text-walnut-900">{{ number_format($averageRating, 1) }}</span>
                    <div class="flex text-gold-500 gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" class="w-4 h-4 {{ $i <= $averageRating ? 'text-gold-500 fill-gold-500' : 'text-walnut-800/20' }}"></i>
                        @endfor
                    </div>
                    <span class="text-[0.65rem] text-muted font-bold uppercase tracking-widest">({{ $product->reviews->count() }})</span>
                </div>
            </div>

            @if($product->reviews->isEmpty())
                <div class="py-12 text-center text-muted space-y-4 border border-walnut-800/10 bg-cream-50">
                    <p class="text-[0.75rem] font-bold uppercase tracking-widest">Belum Ada Ulasan</p>
                    <p class="text-[0.7rem]">Jadilah yang pertama memiliki dan mengulas mahakarya ini.</p>
                </div>
            @else
                <div class="space-y-6 max-h-[600px] overflow-y-auto pr-4 scrollbar-hide">
                    @foreach($product->reviews as $review)
                        <div class="pb-6 border-b border-walnut-800/10 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-walnut-900 text-gold-500 flex items-center justify-center rounded-none text-[0.6rem] font-bold uppercase">
                                        {{ substr($review->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="text-[0.75rem] font-bold text-walnut-950 uppercase tracking-widest">{{ $review->user->name }}</h4>
                                        <span class="text-[0.6rem] text-muted font-bold">{{ $review->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i data-lucide="star" class="w-3 h-3 {{ $i <= $review->rating ? 'text-gold-500 fill-gold-500' : 'text-walnut-800/20' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-muted text-[0.8rem] leading-relaxed font-medium">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="space-y-12 pt-16 border-t border-walnut-800/10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block mb-2">Kurasi Serupa</span>
                    <h2 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Koleksi <span class="text-gold-500">Terkait.</span></h2>
                </div>
            </div>
            
            <div class="grid gap-8 md:grid-cols-3">
                @foreach($relatedProducts as $rel)
                    <article class="group flex flex-col h-full">
                        <a href="{{ route('products.show', $rel->slug) }}" class="block mb-4 relative overflow-hidden bg-cream-50 border border-walnut-800/5 h-[280px]">
                            <img src="{{ $rel->primaryImage ? $rel->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $rel->name }}" class="h-full w-full object-cover mix-blend-multiply opacity-90 transition duration-700 group-hover:scale-105 group-hover:opacity-100" />
                        </a>
                        <div class="space-y-2">
                            <span class="text-[0.55rem] uppercase tracking-[0.2em] text-gold-600 font-bold">{{ $rel->category->name }}</span>
                            <h3 class="font-display text-base font-bold uppercase tracking-tight text-walnut-950 leading-snug group-hover:text-gold-600 transition">{{ $rel->name }}</h3>
                            <p class="text-[0.75rem] font-bold tracking-widest text-walnut-900">IDR {{ number_format($rel->price, 0, ',', '.') }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
