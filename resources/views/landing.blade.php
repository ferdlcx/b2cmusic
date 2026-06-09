@extends('layouts.app')

@section('title', 'DjudasMS - Premium Music Instruments')

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

<!-- Editorial Hero Section -->
<section class="relative w-screen left-1/2 -ml-[50vw] -mt-[5.5rem] pt-[8.5rem] pb-16 overflow-hidden bg-cream-100" 
         x-data="{ scrollY: 0 }" 
         @scroll.window="scrollY = window.scrollY">
    
    <!-- Hero Content Container -->
    <div class="relative min-h-[85vh] flex flex-col justify-center px-6 lg:px-12 max-w-[1440px] mx-auto z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center h-full relative">
            
            <!-- Left Column: Typography -->
            <div class="lg:col-span-8 z-20 flex flex-col justify-center">
                <div class="mb-6 animate-fade-in-up" style="animation-delay: 0.1s">
                    <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold">
                        B2C E-Commerce Model
                    </span>
                </div>
                
                <!-- Main Heading (Editorial Style) -->
                <h1 class="font-display text-[4rem] sm:text-[6rem] md:text-[8rem] lg:text-[10rem] leading-[0.85] font-black uppercase tracking-tighter text-walnut-950 mix-blend-multiply">
                    <span class="block animate-fade-in-up" style="animation-delay: 0.3s">Pure.</span>
                    <span class="block animate-fade-in-up" style="animation-delay: 0.5s">Craft.</span>
                    <span class="block text-gold-500 animate-fade-in-up" style="animation-delay: 0.7s">Iconic.</span>
                </h1>
                
                <!-- Subtitle -->
                <p class="mt-12 max-w-md text-sm md:text-base text-muted leading-relaxed font-medium animate-fade-in-up" style="animation-delay: 0.9s">
                    Kurasi instrumen musik kelas dunia dan perlengkapan rekaman otentik. Temukan karya seni sejati untuk menyempurnakan harmoni Anda.
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 mt-12 animate-fade-in-up" style="animation-delay: 1.1s">
                    <a href="{{ route('catalog') }}" class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-walnut-900 text-cream-50 text-[0.75rem] font-bold tracking-[0.2em] uppercase hover:bg-gold-600 transition-all duration-500">
                        Jelajahi Toko
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-2 transition-transform duration-300"></i>
                    </a>
                </div>
            </div>
            
            <!-- Right Column: Parallax Image -->
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-full lg:w-[55%] max-w-[800px] z-10 pointer-events-none hidden md:block">
                <img src="{{ asset('images/hero_guitar.png') }}" 
                     alt="Premium Guitar" 
                     class="w-full h-auto object-contain mix-blend-multiply opacity-90 drop-shadow-2xl"
                     :style="'transform: translateY(' + (scrollY * -0.15) + 'px) scale(1.05)'" />
            </div>

            <!-- Mobile Image Fallback -->
            <div class="md:hidden w-full h-[300px] mt-8 relative">
                <img src="{{ asset('images/hero_guitar.png') }}" 
                     alt="Premium Guitar" 
                     class="w-full h-full object-contain mix-blend-multiply opacity-90 drop-shadow-xl" />
            </div>
        </div>
        
        <!-- Rotating Badge -->
        <div class="absolute bottom-12 right-12 hidden lg:flex items-center justify-center w-32 h-32 animate-fade-in-up z-30" style="animation-delay: 1.5s">
            <div class="relative w-full h-full flex items-center justify-center">
                <i data-lucide="award" class="w-8 h-8 text-gold-600 absolute"></i>
                <svg viewBox="0 0 100 100" class="w-full h-full animate-spin-slow">
                    <path id="circlePath" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0" fill="transparent" />
                    <text>
                        <textPath href="#circlePath" class="text-[0.6rem] font-bold uppercase tracking-[0.2em]" fill="#5c4033">
                            • Premium Quality • Authentic Sound 
                        </textPath>
                    </text>
                </svg>
            </div>
        </div>
        
        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-8 left-6 lg:left-12 flex items-center gap-4 animate-fade-in-up" style="animation-delay: 1.5s">
            <div class="w-[1px] h-12 bg-walnut-800/20 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-full h-1/2 bg-walnut-800 animate-bounce-slow"></div>
            </div>
            <span class="text-[0.6rem] uppercase tracking-[0.3em] text-walnut-800 font-bold origin-left -rotate-90 translate-y-8">Scroll</span>
        </div>
    </div>
</section>

<!-- Collections Section (Editorial Asymmetric) -->
<section id="collections" class="py-24">
    <div class="space-y-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 border-b border-walnut-800/10 pb-8">
            <h2 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">
                Kategori <br> <span class="text-gold-500">Koleksi.</span>
            </h2>
            <p class="text-muted text-sm max-w-sm leading-relaxed">
                Eksplorasi instrumen pilihan dari piringan hitam klasik hingga alat musik modern dengan presisi tinggi.
            </p>
        </div>
        
        <div class="grid gap-x-8 gap-y-16 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($categories as $cat)
                @php
                    $icon = 'music';
                    $slug = strtolower($cat->slug);
                    if (str_contains($slug, 'gitar') || str_contains($slug, 'bass') || str_contains($slug, 'ukulele')) $icon = 'guitar';
                    elseif (str_contains($slug, 'drum')) $icon = 'drum';
                    elseif (str_contains($slug, 'keyboard') || str_contains($slug, 'piano')) $icon = 'piano';
                    elseif (str_contains($slug, 'audio') || str_contains($slug, 'recording')) $icon = 'mic';
                @endphp
                <a href="{{ route('catalog', ['category' => $cat->slug]) }}" class="group block space-y-6">
                    <div class="h-[2px] w-12 bg-gold-500 group-hover:w-full transition-all duration-500"></div>
                    <div class="flex items-start justify-between">
                        <i data-lucide="{{ $icon }}" class="w-6 h-6 text-walnut-800 group-hover:text-gold-600 transition duration-300"></i>
                        <span class="text-[0.65rem] text-muted font-bold tracking-widest">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="space-y-3">
                        <h3 class="font-display text-xl font-black uppercase tracking-tight text-walnut-950 group-hover:text-gold-600 transition">{{ $cat->name }}</h3>
                        <p class="text-[0.8rem] text-muted leading-relaxed line-clamp-2">{{ $cat->description }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="products" class="py-24 bg-cream-200 -mx-6 lg:-mx-10 px-6 lg:px-10 my-16 border-y border-walnut-800/5">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-16">
        <div>
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block mb-4">Featured Selection</span>
            <h2 class="font-display text-3xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Karya Terpilih.</h2>
        </div>
        <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 text-[0.7rem] uppercase tracking-[0.2em] font-bold text-walnut-900 hover:text-gold-600 transition group">
            Lihat Semua Koleksi 
            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
    
    <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-3">
        @forelse($products as $product)
            <article class="group flex flex-col h-full">
                <a href="{{ route('products.show', $product->slug) }}" class="block mb-6 relative overflow-hidden bg-cream-50 border border-walnut-800/5 h-[320px]">
                    <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1200&q=80' }}" 
                         alt="{{ $product->name }}" 
                         class="h-full w-full object-cover mix-blend-multiply opacity-90 transition duration-700 group-hover:scale-105 group-hover:opacity-100" />
                    
                    <div class="absolute top-4 left-4">
                        <span class="text-[0.6rem] uppercase tracking-[0.2em] font-bold bg-walnut-900 text-cream-50 px-3 py-1.5">
                            {{ $product->brand }}
                        </span>
                    </div>
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

                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="text-walnut-800 hover:text-gold-600 transition" title="Tambah ke Keranjang">
                                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-3 text-center py-20 text-muted text-sm uppercase tracking-widest">Koleksi belum tersedia.</div>
        @endforelse
    </div>
</section>

<!-- Editorial Story Section -->
<section class="py-24 grid gap-16 lg:grid-cols-2 lg:items-center">
    <div class="space-y-8 pr-0 lg:pr-12">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Filosofi Kami</span>
        <h2 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950 leading-[0.9]">
            Keindahan <br>Dalam Presisi.
        </h2>
        <p class="text-muted leading-relaxed text-sm md:text-base">
            DjudasMS bukan sekadar etalase instrumen. Kami adalah kurator yang percaya bahwa estetika visual dan akurasi sonik adalah dua hal yang tak terpisahkan. Setiap koleksi yang kami tampilkan telah melewati uji dengar dan raba yang ketat.
        </p>
        <a href="#" class="inline-block border-b-2 border-walnut-900 pb-1 text-[0.7rem] uppercase tracking-[0.2em] font-bold text-walnut-900 hover:text-gold-600 hover:border-gold-600 transition">
            Baca Kisah Kami
        </a>
    </div>
    
    <div class="grid sm:grid-cols-2 gap-8">
        <div class="space-y-4 pt-8 border-t border-walnut-800/10">
            <div class="font-display text-4xl font-black text-gold-500">01</div>
            <div class="text-[0.7rem] uppercase tracking-[0.2em] font-bold text-walnut-950">Kurasi Ketat</div>
            <p class="text-[0.8rem] text-muted leading-relaxed">Pilihan material dan resonansi diuji sebelum dimasukkan ke dalam katalog.</p>
        </div>
        <div class="space-y-4 pt-8 border-t border-walnut-800/10 sm:mt-12">
            <div class="font-display text-4xl font-black text-gold-500">02</div>
            <div class="text-[0.7rem] uppercase tracking-[0.2em] font-bold text-walnut-950">Garansi Orisinal</div>
            <p class="text-[0.8rem] text-muted leading-relaxed">Jaminan keaslian seumur hidup untuk investasi musik berharga Anda.</p>
        </div>
    </div>
</section>
@endsection
