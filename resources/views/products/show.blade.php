@extends('layouts.app')

@section('title', $product->name . ' - MusicStore Luxe')

@section('content')
<div class="space-y-16 py-4">
    <!-- Breadcrumb -->
    <nav class="text-xs uppercase tracking-widest text-slate-400 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
        <span>/</span>
        <a href="{{ route('catalog') }}" class="hover:text-slate-900">Shop</a>
        <span>/</span>
        <a href="{{ route('catalog', ['category' => $product->category->slug]) }}" class="hover:text-slate-900">{{ $product->category->name }}</a>
        <span>/</span>
        <span class="text-slate-900">{{ $product->name }}</span>
    </nav>

    <!-- Product Intro Block -->
    <div class="grid gap-12 lg:grid-cols-2">
        <!-- Left: Images Gallery & Video -->
        <div class="space-y-6">
            <!-- Main Image -->
            <div class="overflow-hidden rounded-[40px] bg-white border border-slate-200 p-2 shadow-[0_20px_50px_rgba(15,23,42,0.03)] flex items-center justify-center min-h-[400px]">
                <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $product->name }}" class="rounded-[32px] w-full max-h-[500px] object-cover" />
            </div>

            <!-- Gallery Images -->
            @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-4">
                    @foreach($product->images as $img)
                        <div class="overflow-hidden rounded-2xl bg-white border border-slate-200 p-1 cursor-pointer hover:border-slate-950 transition">
                            <img src="{{ $img->image }}" alt="Gallery image" class="rounded-xl w-full h-20 object-cover" />
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Embed Video Demonstration -->
            @if($product->videos->isNotEmpty())
                <div class="space-y-3 pt-4">
                    <span class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Video Demonstrasi & Review</span>
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 shadow-sm aspect-video">
                        <iframe class="absolute inset-0 w-full h-full" src="{{ $product->videos->first()->video_url }}" title="{{ $product->videos->first()->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Information & Buy -->
        <div class="space-y-8">
            <div class="space-y-4">
                <span class="text-xs uppercase tracking-[0.25em] text-slate-500 font-black">{{ $product->category->name }}</span>
                <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-slate-950 leading-tight">{{ $product->name }}</h1>
                
                <div class="flex items-center gap-6 pt-2">
                    <span class="text-xs text-slate-400 font-medium">Merek: <strong class="text-slate-800">{{ $product->brand ?: '-' }}</strong></span>
                    <span class="h-4 w-px bg-slate-200"></span>
                    <span class="text-xs text-slate-400 font-medium">SKU: <strong class="text-slate-800">{{ $product->sku }}</strong></span>
                </div>
            </div>

            <!-- Price & Stock -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-[0_20px_50px_rgba(15,23,42,0.03)] space-y-6">
                <div>
                    <span class="text-xs uppercase tracking-widest text-slate-400 font-bold block mb-1">Harga</span>
                    <span class="text-3xl font-black text-slate-950">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center gap-4">
                    @if($product->stock > 0)
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Stok Tersedia ({{ $product->stock }} unit)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            Stok Habis
                        </span>
                    @endif
                </div>

                <!-- Add to Cart Form -->
                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex gap-4">
                            <div class="w-32 space-y-2">
                                <label for="quantity" class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Jumlah</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}"
                                    class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white text-center text-sm font-bold" />
                            </div>
                            <div class="flex-1 flex flex-col justify-end">
                                <button type="submit" 
                                    class="w-full py-4 bg-slate-950 text-white rounded-xl font-black uppercase text-xs tracking-[0.2em] hover:bg-slate-800 transition">
                                    Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <button disabled class="w-full py-4 bg-slate-200 text-slate-400 rounded-xl font-black uppercase text-xs tracking-[0.2em] cursor-not-allowed">
                        Stok Habis Terjual
                    </button>
                @endif
            </div>

            <!-- Short Description -->
            <div class="space-y-3">
                <span class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Deskripsi Singkat</span>
                <p class="text-slate-600 leading-relaxed text-sm">{{ $product->short_description }}</p>
            </div>
        </div>
    </div>

    <!-- Details Tab & Review Grid -->
    <div class="grid gap-12 lg:grid-cols-2 pt-8 border-t border-slate-200">
        <!-- Specifications -->
        <div class="space-y-6">
            <h2 class="text-2xl font-black uppercase tracking-tight text-slate-950">Spesifikasi Detail</h2>
            @if($product->specifications->isEmpty())
                <p class="text-sm text-slate-400">Tidak ada spesifikasi khusus untuk produk ini.</p>
            @else
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                    <table class="w-full text-sm text-left">
                        <tbody>
                            @foreach($product->specifications as $spec)
                                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50">
                                    <td class="px-6 py-4 font-bold text-slate-500 w-1/3 bg-slate-50/50">{{ $spec->spec_name }}</td>
                                    <td class="px-6 py-4 text-slate-800">{{ $spec->spec_value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="space-y-4 pt-4">
                <h3 class="text-lg font-bold text-slate-900 uppercase tracking-tight">Keterangan Produk</h3>
                <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
            </div>
        </div>

        <!-- Reviews -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black uppercase tracking-tight text-slate-950">Ulasan Pembeli</h2>
                <div class="flex items-center gap-2">
                    <span class="text-lg font-bold text-slate-900">{{ number_format($averageRating, 1) }}</span>
                    <div class="flex text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 {{ $i <= $averageRating ? 'text-amber-400' : 'text-slate-200' }}">
                                <path fill-rule="evenodd" d="M10.788 2.903a.75.75 0 011.424 0l2.082 5.007 5.404.433a.75.75 0 01.428 1.353l-4.03 3.693.815 5.38a.75.75 0 01-1.12.836L12 17.65l-4.72 2.59a.75.75 0 01-1.12-.836l.815-5.38-4.03-3.693a.75.75 0 01.428-1.353l5.404-.433 2.08-5.006z" clip-rule="evenodd" />
                            </svg>
                        @endfor
                    </div>
                    <span class="text-xs text-slate-400 font-medium">({{ $product->reviews->count() }} ulasan)</span>
                </div>
            </div>

            @if($product->reviews->isEmpty())
                <div class="bg-white border border-slate-200 rounded-[32px] p-10 text-center text-slate-500">
                    <p class="text-sm font-bold text-slate-900">Belum Ada Ulasan</p>
                    <p class="text-xs mt-1 text-slate-400">Jadilah yang pertama memberikan ulasan untuk produk ini!</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($product->reviews as $review)
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $review->user->name }}</h4>
                                    <span class="text-[0.65rem] text-slate-400">{{ $review->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}">
                                            <path fill-rule="evenodd" d="M10.788 2.903a.75.75 0 011.424 0l2.082 5.007 5.404.433a.75.75 0 01.428 1.353l-4.03 3.693.815 5.38a.75.75 0 01-1.12.836L12 17.65l-4.72 2.59a.75.75 0 01-1.12-.836l.815-5.38-4.03-3.693a.75.75 0 01.428-1.353l5.404-.433 2.08-5.006z" clip-rule="evenodd" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="space-y-8 pt-8 border-t border-slate-200">
            <div>
                <span class="text-xs uppercase tracking-widest text-slate-500 font-bold block mb-1">Rekomendasi</span>
                <h2 class="text-3xl font-black uppercase tracking-tight text-slate-950">Produk Terkait</h2>
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                @foreach($relatedProducts as $rel)
                    <article class="group overflow-hidden rounded-[32px] bg-white border border-slate-200 shadow-sm transition hover:-translate-y-1">
                        <a href="{{ route('products.show', $rel->slug) }}" class="block">
                            <div class="h-56 overflow-hidden bg-slate-50 flex items-center justify-center">
                                <img src="{{ $rel->primaryImage ? $rel->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $rel->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            </div>
                            <div class="p-6">
                                <span class="text-[0.65rem] uppercase tracking-[0.25em] text-slate-500 font-bold block">{{ $rel->category->name }}</span>
                                <h3 class="mt-3 text-base font-black uppercase tracking-tight text-slate-950 leading-snug line-clamp-2 h-12">{{ $rel->name }}</h3>
                                <p class="mt-3 text-base font-black text-slate-900">Rp {{ number_format($rel->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
