@extends('layouts.app')

@section('title', $product->name . ' - DjudasMS')

@section('content')
<div class="space-y-16 py-4">
    <!-- Breadcrumb -->
    <nav class="text-xs font-semibold uppercase tracking-wider text-slate-400 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-indigo-600 flex items-center gap-1 transition">
            <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <a href="{{ route('catalog') }}" class="hover:text-indigo-600 transition">Shop</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <a href="{{ route('catalog', ['category' => $product->category->slug]) }}" class="hover:text-indigo-600 transition">{{ $product->category->name }}</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-slate-900 font-bold truncate max-w-[200px]">{{ $product->name }}</span>
    </nav>

    <!-- Product Intro Block -->
    <div x-data="{ 
        activeImage: '{{ $product->primaryImage ? $product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}' 
    }" class="grid gap-12 lg:grid-cols-2">
        
        <!-- Left: Images Gallery & Video -->
        <div class="space-y-6">
            <!-- Main Image Frame with zoom-on-hover -->
            <div class="overflow-hidden rounded-[36px] bg-white border border-slate-200/80 p-3 shadow-sm flex items-center justify-center min-h-[350px] md:min-h-[450px]">
                <img :src="activeImage" alt="{{ $product->name }}" class="rounded-[28px] w-full max-h-[500px] object-cover transition-transform duration-700 hover:scale-103" />
            </div>

            <!-- Gallery Images (Alpine switcher) -->
            @if($product->images->count() > 0)
                <div class="grid grid-cols-5 gap-3">
                    <!-- Primary Image Thumbnail -->
                    @if($product->primaryImage)
                        <div @click="activeImage = '{{ $product->primaryImage->image }}'" 
                             :class="activeImage === '{{ $product->primaryImage->image }}' ? 'border-indigo-600 shadow-md scale-98' : 'border-slate-200/80'"
                             class="overflow-hidden rounded-2xl bg-white border-2 p-1 cursor-pointer transition duration-300">
                            <img src="{{ $product->primaryImage->image }}" alt="Thumbnail" class="rounded-xl w-full h-14 md:h-16 object-cover" />
                        </div>
                    @endif
                    <!-- Secondary Images -->
                    @foreach($product->images->where('is_primary', false) as $img)
                        <div @click="activeImage = '{{ $img->image }}'" 
                             :class="activeImage === '{{ $img->image }}' ? 'border-indigo-600 shadow-md scale-98' : 'border-slate-200/80'"
                             class="overflow-hidden rounded-2xl bg-white border-2 p-1 cursor-pointer transition duration-300">
                            <img src="{{ $img->image }}" alt="Thumbnail" class="rounded-xl w-full h-14 md:h-16 object-cover" />
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Embed Video Demonstration -->
            @if($product->videos->isNotEmpty())
                <div class="space-y-4 pt-6 border-t border-slate-200/60">
                    <span class="text-xs uppercase tracking-widest text-slate-900 font-black block">Video Demo & Sound Check</span>
                    <div class="relative overflow-hidden rounded-[32px] border border-slate-200/80 shadow-sm aspect-video">
                        <iframe class="absolute inset-0 w-full h-full" src="{{ $product->videos->first()->video_url }}" title="{{ $product->videos->first()->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Information & Buy -->
        <div class="space-y-8">
            <div class="space-y-3">
                <span class="text-[0.65rem] uppercase tracking-[0.25em] text-indigo-600 font-bold bg-indigo-50 px-3 py-1.5 rounded-full inline-block">{{ $product->category->name }}</span>
                <h1 class="font-display text-3xl md:text-4xl font-black uppercase tracking-tight text-slate-950 leading-tight">{{ $product->name }}</h1>
                
                <div class="flex items-center gap-4 pt-2">
                    <span class="text-xs text-slate-400 font-semibold">Merek: <strong class="text-slate-700 font-bold">{{ $product->brand ?: '-' }}</strong></span>
                    <span class="h-3 w-px bg-slate-200"></span>
                    <span class="text-xs text-slate-400 font-semibold">SKU: <strong class="text-slate-700 font-bold">{{ $product->sku }}</strong></span>
                </div>
            </div>

            <!-- Price & Stock Card -->
            <div class="card-shine bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-6">
                <div class="space-y-1">
                    <span class="text-xs uppercase tracking-widest text-slate-400 font-semibold block">Harga</span>
                    <span class="text-3xl font-black text-indigo-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center gap-4">
                    @if($product->stock > 0)
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Stok Tersedia ({{ $product->stock }} unit)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-100">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            Stok Habis
                        </span>
                    @endif
                </div>

                <!-- Add to Cart Form (Interactive Alpine qty) -->
                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div x-data="{ qty: 1, maxQty: {{ $product->stock }} }" class="flex flex-col sm:flex-row gap-4 items-end">
                            <div class="w-full sm:w-36 space-y-2">
                                <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Jumlah</label>
                                <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden">
                                    <button type="button" @click="if (qty > 1) qty--" class="px-4 py-3 hover:bg-slate-100 text-slate-500 font-black focus:outline-none transition">-</button>
                                    <input type="number" name="quantity" x-model="qty" readonly class="w-10 bg-transparent text-center text-sm font-black text-slate-800 focus:outline-none" />
                                    <button type="button" @click="if (qty < maxQty) qty++" class="px-4 py-3 hover:bg-slate-100 text-slate-500 font-black focus:outline-none transition">+</button>
                                </div>
                            </div>
                            
                            <div class="flex-1 w-full">
                                <button type="submit" 
                                    class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                                    <i data-lucide="shopping-cart" class="w-4 h-4"></i> Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <button disabled class="w-full py-4 bg-slate-100 text-slate-400 border border-slate-200/80 rounded-2xl font-semibold uppercase text-xs tracking-wider cursor-not-allowed">
                        Stok Habis Terjual
                    </button>
                @endif
            </div>

            <!-- Short Description -->
            <div class="space-y-3">
                <span class="text-xs uppercase tracking-widest text-slate-900 font-black block">Deskripsi Singkat</span>
                <p class="text-slate-600 leading-relaxed text-sm font-normal">{{ $product->short_description }}</p>
            </div>
        </div>
    </div>

    <!-- Details Tab & Review Grid -->
    <div class="grid gap-12 lg:grid-cols-2 pt-8 border-t border-slate-200/60">
        <!-- Specifications -->
        <div class="space-y-6">
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-slate-950">Spesifikasi Detail</h2>
            @if($product->specifications->isEmpty())
                <p class="text-sm text-slate-400">Tidak ada spesifikasi khusus untuk produk ini.</p>
            @else
                <div class="card-shine overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
                    <table class="w-full text-xs text-left">
                        <tbody>
                            @foreach($product->specifications as $spec)
                                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition">
                                    <td class="px-6 py-4.5 font-bold text-slate-500 w-1/3 bg-slate-50/50">{{ $spec->spec_name }}</td>
                                    <td class="px-6 py-4.5 text-slate-800 font-semibold">{{ $spec->spec_value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="space-y-4 pt-4">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Keterangan Lengkap</h3>
                <div class="prose prose-sm prose-slate max-w-none text-slate-600 font-normal">
                    {!! $product->description !!}
                </div>
            </div>
        </div>

        <!-- Reviews -->
        <div class="space-y-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <h2 class="font-display text-2xl font-black uppercase tracking-tight text-slate-950">Ulasan Pembeli</h2>
                <div class="flex items-center gap-2">
                    <span class="text-lg font-black text-slate-900">{{ number_format($averageRating, 1) }}</span>
                    <div class="flex text-amber-400 gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" class="w-4 h-4 {{ $i <= $averageRating ? 'text-amber-500 fill-amber-500' : 'text-slate-200' }}"></i>
                        @endfor
                    </div>
                    <span class="text-xs text-slate-400 font-semibold">({{ $product->reviews->count() }} ulasan)</span>
                </div>
            </div>

            @if($product->reviews->isEmpty())
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-10 text-center text-slate-400 shadow-sm space-y-2">
                    <div class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-300">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Belum Ada Ulasan</p>
                        <p class="text-xs text-slate-400">Jadilah yang pertama memberikan ulasan untuk produk ini!</p>
                    </div>
                </div>
            @else
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                    @foreach($product->reviews as $review)
                        <div class="card-shine bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-indigo-50 text-indigo-700 flex items-center justify-center rounded-full text-xs font-bold uppercase">
                                        {{ substr($review->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900">{{ $review->user->name }}</h4>
                                        <span class="text-[0.6rem] text-slate-400 font-medium">{{ $review->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                </div>
                                <div class="flex text-amber-500 gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i data-lucide="star" class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-500 fill-amber-500' : 'text-slate-200' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-slate-600 text-xs leading-relaxed font-normal">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="space-y-8 pt-8 border-t border-slate-200/60">
            <div class="space-y-2">
                <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Rekomendasi</span>
                <h2 class="font-display text-3xl font-black uppercase tracking-tight text-slate-950">Produk Terkait</h2>
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                @foreach($relatedProducts as $rel)
                    <article class="card-shine group overflow-hidden rounded-[32px] bg-white border border-slate-200/80 shadow-sm transition hover:shadow-xl hover:-translate-y-1.5 duration-300 flex flex-col justify-between h-full">
                        <a href="{{ route('products.show', $rel->slug) }}" class="block">
                            <div class="h-52 overflow-hidden bg-slate-50 flex items-center justify-center">
                                <img src="{{ $rel->primaryImage ? $rel->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $rel->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-103" />
                            </div>
                            <div class="p-6 space-y-2">
                                <span class="text-[0.65rem] uppercase tracking-[0.25em] text-slate-400 font-bold block">{{ $rel->category->name }}</span>
                                <h3 class="font-display text-base font-bold uppercase tracking-tight text-slate-950 leading-snug line-clamp-2 h-12 group-hover:text-indigo-600 transition">{{ $rel->name }}</h3>
                                <p class="text-base font-black text-indigo-600">Rp {{ number_format($rel->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
