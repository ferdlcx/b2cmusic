@extends('layouts.app')

@section('title', 'DjudasMS - Premium Instruments & Records')

@section('content')
<!-- Parallax Hero Section -->
<section class="relative -mx-6 lg:-mx-10 -mt-4 overflow-hidden" x-data="{ 
    mouseX: 0, mouseY: 0,
    handleMouse(e) {
        this.mouseX = (e.clientX / window.innerWidth - 0.5) * 30;
        this.mouseY = (e.clientY / window.innerHeight - 0.5) * 30;
    }
}" @mousemove.window="handleMouse($event)">
    <!-- Parallax Background -->
    <div class="absolute inset-0 w-full h-full" 
         :style="'transform: translate(' + mouseX + 'px, ' + mouseY + 'px) scale(1.1)'">
        <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=2000&q=80" 
             alt="Music Studio" class="w-full h-full object-cover" />
    </div>
    
    <!-- Dark Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-slate-950/60 to-[#f8f8f8]"></div>
    
    <!-- Animated Grid Pattern -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, rgba(255,255,255,0.15) 1px, transparent 1px); background-size: 40px 40px;"></div>

    <!-- Hero Content -->
    <div class="relative min-h-[92vh] flex flex-col justify-center px-6 lg:px-10 max-w-[1440px] mx-auto">
        <div class="space-y-8 max-w-3xl">
            <!-- Animated Badge -->
            <div class="inline-flex items-center gap-3 text-[0.65rem] font-bold uppercase tracking-[0.45em] text-indigo-300 bg-white/5 backdrop-blur-md border border-white/10 px-5 py-2.5 rounded-full animate-fade-in-up" style="animation-delay: 0.2s">
                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                ARTISAN TOKO MUSIK B2C
            </div>
            
            <!-- Main Heading with Staggered Animation -->
            <h1 class="font-display text-5xl sm:text-6xl md:text-7xl lg:text-[5.5rem] leading-[0.9] font-black uppercase tracking-tight text-white">
                <span class="block animate-fade-in-up" style="animation-delay: 0.4s">Suara Murni.</span>
                <span class="block animate-fade-in-up" style="animation-delay: 0.6s">Craftsmanship</span>
                <span class="block bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent animate-fade-in-up" style="animation-delay: 0.8s">Ikonik.</span>
            </h1>
            
            <!-- Subtitle -->
            <p class="max-w-xl text-lg text-slate-300 leading-relaxed font-light animate-fade-in-up" style="animation-delay: 1s">
                Instrumen musik kelas dunia dan gear rekaman kelas studio untuk menyempurnakan ekspresi seni Anda.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 animate-fade-in-up" style="animation-delay: 1.2s">
                <a href="{{ route('catalog') }}" class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-slate-950 rounded-2xl text-sm font-bold tracking-wider hover:bg-indigo-600 hover:text-white hover:shadow-2xl hover:shadow-indigo-600/30 transition-all duration-500">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i> 
                    Jelajahi Toko
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="#collections" class="inline-flex items-center justify-center px-8 py-4 bg-white/5 backdrop-blur-md border border-white/15 text-white rounded-2xl text-sm font-semibold tracking-wider hover:bg-white/10 hover:border-white/30 transition-all duration-500">
                    Lihat Kategori
                </a>
            </div>
        </div>
        
        <!-- Glassmorphism Stats Cards -->
        <div class="absolute bottom-12 right-6 lg:right-10 hidden lg:flex gap-4 animate-fade-in-up" style="animation-delay: 1.6s">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 text-center min-w-[120px]">
                <div class="text-2xl font-black text-white">500+</div>
                <div class="text-[0.6rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Produk</div>
            </div>
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 text-center min-w-[120px]">
                <div class="text-2xl font-black text-white">50+</div>
                <div class="text-[0.6rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Brand</div>
            </div>
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 text-center min-w-[120px]">
                <div class="text-2xl font-black text-white">4.9</div>
                <div class="text-[0.6rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Rating</div>
            </div>
        </div>
        
        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce-slow">
            <span class="text-[0.55rem] uppercase tracking-[0.3em] text-white/50 font-semibold">Scroll</span>
            <div class="w-6 h-10 border-2 border-white/20 rounded-full flex items-start justify-center p-1.5">
                <div class="w-1.5 h-3 bg-white/40 rounded-full animate-scroll-dot"></div>
            </div>
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
                <a href="{{ route('catalog', ['category' => $cat->slug]) }}" class="group overflow-hidden rounded-3xl border border-slate-200/80 bg-white p-8 shadow-sm hover:shadow-xl hover:border-indigo-600/40 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between h-[230px]">
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
            <article class="group overflow-hidden rounded-[32px] bg-white border border-slate-200/80 shadow-sm transition hover:shadow-xl hover:-translate-y-1 duration-300 flex flex-col justify-between h-full">
                <a href="{{ route('products.show', $product->slug) }}" class="block">
                    <div class="h-64 overflow-hidden bg-slate-100 flex items-center justify-center relative">
                        <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" />
                        
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
            DjudasMS bukan sekadar toko retail alat musik biasa. Kami adalah kurator seni musik. Kami percaya bahwa instrumen yang indah secara visual akan melahirkan melodi yang indah secara emosional.
        </p>
    </div>
    <div class="grid gap-6 sm:grid-cols-2">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-8 shadow-sm space-y-4 hover:-translate-y-1 transition duration-300">
            <div class="text-4xl font-black text-indigo-600">01</div>
            <div class="text-xs uppercase tracking-widest text-slate-900 font-bold">Produk Terkurasi</div>
            <p class="text-xs text-slate-500 leading-relaxed">Setiap gitar dan piringan hitam diuji kualitas bunyinya oleh ahli audio kami sebelum masuk daftar display.</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-8 shadow-sm space-y-4 hover:-translate-y-1 transition duration-300">
            <div class="text-4xl font-black text-indigo-600">02</div>
            <div class="text-xs uppercase tracking-widest text-slate-900 font-bold">Garansi Resmi</div>
            <p class="text-xs text-slate-500 leading-relaxed">Kami menjamin orisinalitas semua produk premium dengan opsi pengembalian dana penuh jika cacat.</p>
        </div>
    </div>
</section>
@endsection
