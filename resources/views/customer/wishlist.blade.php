@extends('layouts.app')

@section('title', 'Wishlist Saya - MusicStore Luxe')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-[0.65rem] uppercase tracking-[0.4em] text-rose-600 font-bold bg-rose-50 px-3.5 py-1.5 rounded-full inline-block">Favorit</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tight text-slate-950 mt-2">Wishlist Saya</h1>
            <p class="text-sm text-slate-500 font-normal">Daftar produk yang Anda sukai dan simpan untuk dibeli nanti.</p>
        </div>
        <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-slate-200 bg-white rounded-2xl text-xs font-semibold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2 text-slate-400"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Wishlist Items Grid -->
    @if($wishlistItems->isEmpty())
        <div class="text-center py-20 bg-white border border-slate-200/80 rounded-[32px] shadow-sm space-y-4 max-w-2xl mx-auto">
            <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center text-rose-500 mx-auto">
                <i data-lucide="heart" class="w-7 h-7"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-display text-lg font-bold uppercase tracking-tight text-slate-950">Wishlist Anda Kosong</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Cari produk-produk instrumen musik impian Anda di katalog toko kami dan masukkan ke wishlist.</p>
            </div>
            <div class="pt-2">
                <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 rounded-2xl text-xs font-semibold uppercase tracking-wider text-white hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300">
                    Mulai Belanja &rarr;
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($wishlistItems as $item)
                @php
                    $product = $item->product;
                @endphp
                <div class="bg-white border border-slate-200/80 rounded-[28px] overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <!-- Product Image -->
                    <div class="relative bg-slate-50 aspect-square overflow-hidden group">
                        <a href="{{ route('products.show', $product->slug) }}">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i data-lucide="image" class="w-12 h-12"></i>
                                </div>
                            @endif
                        </a>
                        
                        <!-- Delete Button (Top Right) -->
                        <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" class="absolute top-4 right-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-slate-400 hover:text-rose-600 hover:scale-105 shadow-sm transition" title="Hapus dari wishlist">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Product Details -->
                    <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
                        <div class="space-y-1">
                            <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">{{ $product->category->name }}</span>
                            <a href="{{ route('products.show', $product->slug) }}" class="font-display font-black text-sm uppercase tracking-tight text-slate-950 hover:text-indigo-600 transition line-clamp-1">
                                {{ $product->name }}
                            </a>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed font-normal">{{ $product->short_description }}</p>
                        </div>

                        <div class="pt-2 flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span class="text-sm font-black text-indigo-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                @if($product->stock < 1)
                                    <span class="text-[0.6rem] font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">Habis</span>
                                @endif
                            </div>

                            @if($product->stock > 0)
                                <form action="{{ route('wishlist.toCart', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold uppercase text-[0.65rem] tracking-widest hover:bg-indigo-700 hover:shadow-md transition duration-300 flex items-center justify-center gap-1.5">
                                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i> Pindahkan ke Keranjang
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full py-3 bg-slate-100 text-slate-400 rounded-xl font-bold uppercase text-[0.65rem] tracking-widest cursor-not-allowed flex items-center justify-center gap-1.5 border border-slate-200/50">
                                    <i data-lucide="slash" class="w-3.5 h-3.5"></i> Stok Kosong
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
