@extends('layouts.app')

@section('title', 'MusicStore Luxe - Premium Instruments & Records')

@section('content')
<!-- Hero Section -->
<section class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] items-center py-8">
    <div class="space-y-8">
        <div class="inline-flex items-center gap-3 text-[0.65rem] font-bold uppercase tracking-[0.45em] text-indigo-600 bg-indigo-50 px-4 py-2 rounded-full">
            <span>ARTISAN TOKO MUSIK B2C</span>
        </div>
        <div class="max-w-3xl">
            <h1 class="font-display text-5xl md:text-6xl lg:text-[4.5rem] leading-tight font-black uppercase tracking-tight text-slate-950">
                Suara Murni.<br />Craftsmanship<br />
                <span class="text-indigo-600">Ikonik.</span>
            </h1>
        </div>
        <p class="max-w-2xl text-md md:text-lg text-slate-600 leading-relaxed font-normal">
            Selamat datang di MusicStore Luxe. Kami menyediakan instrumen musik kelas dunia, piringan hitam legendaris, dan gear rekaman kelas studio untuk menyempurnakan ekspresi seni Anda.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 pt-2">
            <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4.5 bg-indigo-600 text-white rounded-2xl text-sm font-semibold tracking-wider hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/25 transition duration-300">
                <i data-lucide="shopping-bag" class="w-4.5 h-4.5"></i> Jelajahi Toko
            </a>
            <a href="#collections" class="inline-flex items-center justify-center px-8 py-4.5 bg-white border border-slate-200 text-slate-700 rounded-2xl text-sm font-semibold tracking-wider hover:border-indigo-600 hover:text-indigo-600 transition duration-300">
                Lihat Kategori
            </a>
        </div>
    </div>

    <!-- Right Graphic: Interactive Slider -->
    <div x-data="{ 
        activeSlide: 0, 
        slides: [
            {
                image: 'https://images.unsplash.com/photo-1564186763535-ebb21ef5277f?auto=format&fit=crop&w=1200&q=80',
                title: 'Fender Electric Player Stratocaster',
                tagline: 'FENDER SIGNATURE SERIES',
                link: '{{ route('catalog', ['category' => 'gitar-elektrik']) }}'
            },
            {
                image: 'https://images.unsplash.com/photo-1552422535-c45813c61732?auto=format&fit=crop&w=1200&q=80',
                title: 'Yamaha PSR Series Keyboards',
                tagline: 'YAMAHA DIGITAL INSTRUMENTS',
                link: '{{ route('catalog', ['category' => 'keyboard-piano']) }}'
            },
            {
                image: 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=1200&q=80',
                title: 'Professional Studio Recording Gear',
                tagline: 'FOCUSRITE & SHURE AUDIO GEAR',
                link: '{{ route('catalog', ['category' => 'audio-recording']) }}'
            }
        ],
        next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
        prev() { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length }
    }" x-init="setInterval(() => next(), 5000)" class="relative overflow-hidden rounded-[36px] bg-slate-950 shadow-2xl h-[420px] lg:h-[480px] group border border-slate-900">
        <!-- Slides -->
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="activeSlide === index" 
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 scale-102 translate-x-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="opacity-100 scale-100 translate-x-0"
                 x-transition:leave-end="opacity-0 scale-98 -translate-x-4"
                 class="absolute inset-0 w-full h-full">
                <!-- Background Image -->
                <img :src="slide.image" alt="" class="h-full w-full object-cover opacity-60 transform group-hover:scale-102 transition duration-700" />
                <!-- Overlay Gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                <!-- Slide Content -->
                <div class="absolute bottom-0 left-0 right-0 p-8 md:p-10 space-y-3 max-w-2xl text-left">
                    <span class="text-[0.65rem] uppercase tracking-[0.35em] font-black text-indigo-400" x-text="slide.tagline"></span>
                    <h2 class="text-2xl md:text-3xl font-display font-black text-white leading-tight uppercase tracking-tight" x-text="slide.title"></h2>
                    <div class="pt-2">
                        <a :href="slide.link" class="inline-flex items-center gap-1.5 text-xs uppercase tracking-widest text-white font-bold hover:text-indigo-400 transition duration-300">
                            Jelajahi Koleksi <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </template>

        <!-- Navigation Arrows -->
        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white border border-white/10 opacity-0 group-hover:opacity-100 transition duration-300">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </button>
        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white border border-white/10 opacity-0 group-hover:opacity-100 transition duration-300">
            <i data-lucide="chevron-right" class="w-5 h-5"></i>
        </button>

        <!-- Indicators -->
        <div class="absolute bottom-8 right-8 flex gap-2">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index" 
                        :class="activeSlide === index ? 'bg-indigo-600 w-8' : 'bg-white/30 w-2'" 
                        class="h-2 rounded-full transition-all duration-300"></button>
            </template>
        </div>
    </div>
</section>

<!-- Collections Section -->
<section id="collections" class="py-16 mt-8">
    <div class="space-y-10">
        <div class="text-center md:text-left space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Curated Categories</span>
            <h2 class="font-display text-3xl md:text-4xl font-black uppercase tracking-tight text-slate-950">Kategori Alat Musik</h2>
            <p class="text-slate-500 text-sm max-w-xl">Dari piringan hitam vintage hingga gitar elektrik berkualitas premium.</p>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($categories as $cat)
                @php
                    $icon = 'music';
                    $slug = strtolower($cat->slug);
                    if (str_contains($slug, 'gitar')) $icon = 'guitar';
                    elseif (str_contains($slug, 'bass')) $icon = 'guitar';
                    elseif (str_contains($slug, 'drum')) $icon = 'drum';
                    elseif (str_contains($slug, 'keyboard') || str_contains($slug, 'piano')) $icon = 'piano'; // Lucide piano/music
                    elseif (str_contains($slug, 'tiup') || str_contains($slug, 'saxophone') || str_contains($slug, 'trumpet') || str_contains($slug, 'flute')) $icon = 'wind';
                    elseif (str_contains($slug, 'biola')) $icon = 'music';
                    elseif (str_contains($slug, 'audio') || str_contains($slug, 'recording') || str_contains($slug, 'microphone')) $icon = 'mic';
                    elseif (str_contains($slug, 'effect') || str_contains($slug, 'pedal')) $icon = 'toggle-right';
                    elseif (str_contains($slug, 'ukulele')) $icon = 'guitar';
                    elseif (str_contains($slug, 'harmonica')) $icon = 'music';
                    elseif (str_contains($slug, 'tradisional')) $icon = 'award';
                    elseif (str_contains($slug, 'aksesoris') || str_contains($slug, 'senar') || str_contains($slug, 'capo')) $icon = 'sliders';
                @endphp
                <a href="{{ route('catalog', ['category' => $cat->slug]) }}" class="group overflow-hidden rounded-3xl border border-slate-200/80 bg-white p-8 shadow-sm hover:shadow-xl hover:border-indigo-600/40 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between h-[230px]">
                    <div class="flex items-center justify-between">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition duration-300">
                            <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs text-slate-400 font-semibold group-hover:text-indigo-600 transition">{{ $cat->products()->count() }} Produk</span>
                    </div>
                    <div class="space-y-2">
                        <h3 class="font-display text-xl font-bold uppercase tracking-tight text-slate-950 group-hover:text-indigo-600 transition">{{ $cat->name }}</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">{{ $cat->description }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="products" class="py-16 bg-slate-50 rounded-[44px] border border-slate-200/50 p-8 lg:p-12 shadow-sm my-16">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-12">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Featured Products</span>
            <h2 class="font-display text-3xl md:text-4xl font-black uppercase tracking-tight text-slate-950">Instrumen Pilihan Kami</h2>
        </div>
        <p class="max-w-md text-sm text-slate-500">Pilihan instrumen terbaik teruji suara oleh kurator ahli kami, siap melahirkan melodi indah dalam kreasi seni Anda.</p>
    </div>
    
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @forelse($products as $product)
            <article class="group overflow-hidden rounded-[32px] bg-white border border-slate-200/80 shadow-sm transition hover:shadow-xl hover:-translate-y-1.5 duration-300 flex flex-col justify-between h-full">
                <a href="{{ route('products.show', $product->slug) }}" class="block">
                    <div class="h-64 overflow-hidden bg-slate-100 flex items-center justify-center relative">
                        <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-103" />
                        
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <span class="text-[0.65rem] uppercase tracking-wider font-bold bg-white text-slate-900 shadow-md px-3 py-1.5 rounded-xl">
                                {{ $product->brand }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6 space-y-3">
                        <span class="text-[0.65rem] uppercase tracking-[0.25em] text-slate-400 font-bold block">{{ $product->category->name }}</span>
                        <h3 class="font-display text-lg font-bold uppercase tracking-tight text-slate-950 leading-snug line-clamp-2 h-12 group-hover:text-indigo-600 transition">{{ $product->name }}</h3>
                        
                        <!-- Rating -->
                        <div class="flex items-center gap-1 text-amber-500 text-xs">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-500"></i>
                            <span class="font-bold text-slate-700">4.9</span>
                            <span class="text-slate-400">(Verified Product)</span>
                        </div>
                        
                        <p class="text-lg font-black text-indigo-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </a>
                <div class="px-6 pb-6">
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                        <a href="{{ route('products.show', $product->slug) }}" class="text-xs uppercase tracking-widest text-slate-900 font-bold hover:text-indigo-600 transition">Detail</a>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="inline-flex items-center gap-1.5 text-xs uppercase tracking-wider bg-indigo-600 text-white px-4 py-2.5 rounded-xl hover:bg-indigo-700 transition duration-300 font-semibold shadow-sm hover:shadow-indigo-600/10">
                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i> Beli
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-3 text-center py-16 text-slate-500">Belum ada produk unggulan yang tersedia.</div>
        @endforelse
    </div>
    
    <div class="text-center mt-12">
        <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white border border-slate-900 text-slate-950 uppercase text-xs tracking-widest hover:bg-indigo-600 hover:text-white hover:border-indigo-600 rounded-2xl transition duration-300 font-black">
            Lihat Semua Produk <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
    </div>
</section>

<!-- Editorial Story Section -->
<section class="py-16 grid gap-12 lg:grid-cols-2 lg:items-center">
    <div class="space-y-6">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Kualitas Suara Maksimal</span>
        <h2 class="font-display text-4xl font-black uppercase tracking-tight text-slate-950">Desain Suara & Estetika Tanpa Kompromi.</h2>
        <p class="text-slate-600 leading-relaxed text-sm">
            MusicStore Luxe bukan sekadar toko retail alat musik biasa. Kami adalah kurator seni musik. Kami percaya bahwa instrumen yang indah secara visual akan melahirkan melodi yang indah secara emosional.
        </p>
    </div>
    <div class="grid gap-6 sm:grid-cols-2">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-8 shadow-sm space-y-4 hover:-translate-y-1.5 transition duration-300">
            <div class="text-4xl font-black text-indigo-600">01</div>
            <div class="text-xs uppercase tracking-widest text-slate-900 font-bold">Produk Terkurasi</div>
            <p class="text-xs text-slate-500 leading-relaxed">Setiap gitar dan piringan hitam diuji kualitas bunyinya oleh ahli audio kami sebelum masuk daftar display.</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-8 shadow-sm space-y-4 hover:-translate-y-1.5 transition duration-300">
            <div class="text-4xl font-black text-indigo-600">02</div>
            <div class="text-xs uppercase tracking-widest text-slate-900 font-bold">Garansi Resmi</div>
            <p class="text-xs text-slate-500 leading-relaxed">Kami menjamin orisinalitas semua produk premium dengan opsi pengembalian dana penuh jika cacat.</p>
        </div>
    </div>
</section>
@endsection
