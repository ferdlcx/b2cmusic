@extends('layouts.app')

@section('title', 'Wishlist Saya - DjudasMS')

@section('content')
<div class="space-y-12 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-walnut-800/10">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Koleksi Tersimpan</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Wishlist <span class="text-gold-500">Saya.</span></h1>
            <p class="text-sm text-muted font-medium pt-2">Daftar mahakarya yang Anda kurasi untuk dimiliki.</p>
        </div>
        <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-transparent border border-walnut-800/20 text-walnut-900 text-[0.65rem] font-bold uppercase tracking-widest hover:border-gold-500 hover:text-gold-600 transition">
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Wishlist Items Grid -->
    @if($wishlistItems->isEmpty())
        <div class="text-center py-24 bg-cream-50 border border-walnut-800/10 space-y-6 max-w-2xl mx-auto">
            <i data-lucide="heart" class="w-10 h-10 text-walnut-800/20 mx-auto"></i>
            <div class="space-y-2">
                <h3 class="font-display text-xl font-black uppercase tracking-tighter text-walnut-950">Belum Ada Wishlist</h3>
                <p class="text-[0.8rem] text-muted max-w-sm mx-auto font-medium">Jelajahi dan temukan instrumen impian Anda di galeri kami.</p>
            </div>
            <div class="pt-4">
                <a href="{{ route('catalog') }}" class="inline-flex items-center px-8 py-4 bg-walnut-900 text-gold-500 text-[0.65rem] font-bold uppercase tracking-widest hover:bg-gold-600 hover:text-white transition duration-500">
                    Jelajahi Galeri
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($wishlistItems as $item)
                @php
                    $product = $item->product;
                @endphp
                <div class="group border border-walnut-800/10 flex flex-col justify-between hover:border-gold-500 transition duration-500">
                    <!-- Product Image -->
                    <div class="relative bg-cream-50 aspect-[4/5] overflow-hidden">
                        <a href="{{ route('products.show', $product->slug) }}">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover mix-blend-multiply opacity-90 group-hover:scale-105 group-hover:opacity-100 transition duration-700" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-walnut-300">
                                    <i data-lucide="image" class="w-12 h-12"></i>
                                </div>
                            @endif
                        </a>
                        
                        <!-- Delete Button (Top Right) -->
                        <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition duration-300">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 bg-walnut-900 text-gold-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition duration-300" title="Hapus dari wishlist">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Product Details -->
                    <div class="p-6 space-y-4 flex-1 flex flex-col justify-between bg-transparent">
                        <div class="space-y-2">
                            <span class="text-[0.55rem] uppercase tracking-[0.2em] text-gold-600 font-bold block">{{ $product->category->name }}</span>
                            <a href="{{ route('products.show', $product->slug) }}" class="font-display font-black text-[0.9rem] uppercase tracking-tighter text-walnut-950 hover:text-gold-600 transition line-clamp-2">
                                {{ $product->name }}
                            </a>
                        </div>

                        <div class="pt-4 flex flex-col gap-4 border-t border-walnut-800/10">
                            <div class="flex items-center justify-between">
                                <span class="text-[0.8rem] font-bold tracking-widest text-walnut-900">IDR {{ number_format($product->price, 0, ',', '.') }}</span>
                                @if($product->stock < 1)
                                    <span class="text-[0.55rem] font-bold uppercase tracking-widest text-red-600 border border-red-600 px-2 py-0.5">Sold Out</span>
                                @endif
                            </div>

                            @if($product->stock > 0)
                                <form action="{{ route('wishlist.toCart', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-3 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.65rem] tracking-widest hover:bg-gold-600 hover:text-white transition duration-500">
                                        Pindah ke Keranjang
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full py-3 bg-cream-100 text-walnut-400 font-bold uppercase text-[0.65rem] tracking-widest cursor-not-allowed border border-walnut-800/10">
                                    Tidak Tersedia
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
