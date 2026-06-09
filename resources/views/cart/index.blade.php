@extends('layouts.app')

@section('title', 'Keranjang Belanja - DjudasMS')

@section('content')
<div class="space-y-12 py-8">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-8 space-y-4">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Kantong Belanja</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Keranjang <span class="text-gold-500">Belanja.</span></h1>
    </div>

    @if($cartItems->isEmpty())
        <div class="py-24 text-center space-y-6 bg-cream-50 border border-walnut-800/10">
            <i data-lucide="shopping-bag" class="w-12 h-12 text-walnut-800/20 mx-auto"></i>
            <div class="space-y-2">
                <p class="font-display text-2xl font-black text-walnut-950 uppercase tracking-tighter">Keranjang Kosong</p>
                <p class="text-[0.8rem] text-muted max-w-sm mx-auto font-medium">Koleksi impian Anda menunggu untuk ditemukan. Silakan jelajahi katalog kami.</p>
            </div>
            <div class="pt-4">
                <a href="{{ route('catalog') }}" class="inline-block px-8 py-4 bg-walnut-900 text-gold-500 text-[0.65rem] uppercase tracking-[0.2em] font-bold hover:bg-gold-600 hover:text-white transition duration-300">
                    Eksplorasi Katalog
                </a>
            </div>
        </div>
    @else
        <div class="grid gap-16 lg:grid-cols-[1fr_380px]">
            <!-- Items List -->
            <div class="space-y-8">
                <!-- Desktop Item List -->
                <div class="hidden sm:block">
                    <table class="w-full text-left">
                        <thead class="text-[0.65rem] uppercase tracking-widest text-muted border-b border-walnut-800/20">
                            <tr>
                                <th class="pb-4 font-bold">Karya</th>
                                <th class="pb-4 text-center font-bold">Kuantitas</th>
                                <th class="pb-4 text-right font-bold">Subtotal</th>
                                <th class="pb-4 text-center font-bold"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                                <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-50 transition">
                                    <!-- Product Info -->
                                    <td class="py-6 flex items-center gap-6">
                                        <div class="w-20 h-20 bg-cream-100 flex-shrink-0 flex items-center justify-center border border-walnut-800/5">
                                            <img src="{{ $item->product->primaryImage ? $item->product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=80&q=80' }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover mix-blend-multiply" />
                                        </div>
                                        <div class="space-y-1">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-display font-bold text-walnut-950 hover:text-gold-600 transition line-clamp-1 text-sm uppercase tracking-tight">{{ $item->product->name }}</a>
                                            <span class="text-[0.7rem] text-muted font-bold tracking-widest block">IDR {{ number_format($item->price, 0, ',', '.') }}</span>
                                        </div>
                                    </td>

                                    <!-- Quantity -->
                                    <td class="py-6 text-center">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" x-data="{ qty: {{ $item->quantity }} }" x-ref="formDesktop" class="inline-flex items-center border border-walnut-800/20 w-28 bg-transparent">
                                            @csrf
                                            <button type="button" @click="if (qty > 1) { qty--; $nextTick(() => $refs.formDesktop.submit()) }" class="w-8 py-2 hover:text-gold-600 text-walnut-500 font-bold focus:outline-none transition">-</button>
                                            <input type="number" name="quantity" :value="qty" readonly class="flex-1 bg-transparent text-center text-[0.75rem] font-bold text-walnut-950 focus:outline-none appearance-none m-0" />
                                            <button type="button" @click="if (qty < {{ $item->product->stock }}) { qty++; $nextTick(() => $refs.formDesktop.submit()) }" class="w-8 py-2 hover:text-gold-600 text-walnut-500 font-bold focus:outline-none transition">+</button>
                                        </form>
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="py-6 text-right font-black text-walnut-950 text-[0.8rem] tracking-widest">
                                        IDR {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </td>

                                    <!-- Remove -->
                                    <td class="py-6 text-center pr-2">
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-walnut-400 hover:text-red-600 transition duration-300" title="Hapus">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Item Cards -->
                <div class="sm:hidden space-y-6">
                    @foreach($cartItems as $item)
                        <div class="border-b border-walnut-800/10 pb-6 space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="w-20 h-20 bg-cream-50 flex-shrink-0 flex items-center justify-center border border-walnut-800/5">
                                    <img src="{{ $item->product->primaryImage ? $item->product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=80&q=80' }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover mix-blend-multiply" />
                                </div>
                                <div class="flex-1 space-y-1">
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="font-display font-bold text-walnut-950 hover:text-gold-600 transition text-[0.8rem] uppercase tracking-tight">{{ $item->product->name }}</a>
                                    <span class="text-[0.7rem] text-muted font-bold block tracking-widest">IDR {{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2">
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" x-data="{ qty: {{ $item->quantity }} }" x-ref="formMobile" class="inline-flex items-center border border-walnut-800/20 w-24">
                                    @csrf
                                    <button type="button" @click="if (qty > 1) { qty--; $nextTick(() => $refs.formMobile.submit()) }" class="w-8 py-1.5 hover:text-gold-600 text-walnut-500 font-bold focus:outline-none transition">-</button>
                                    <input type="number" name="quantity" :value="qty" readonly class="flex-1 bg-transparent text-center text-[0.7rem] font-bold text-walnut-950 focus:outline-none appearance-none m-0" />
                                    <button type="button" @click="if (qty < {{ $item->product->stock }}) { qty++; $nextTick(() => $refs.formMobile.submit()) }" class="w-8 py-1.5 hover:text-gold-600 text-walnut-500 font-bold focus:outline-none transition">+</button>
                                </form>
                                
                                <div class="text-right">
                                    <span class="text-[0.75rem] font-black tracking-widest text-walnut-950">IDR {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                </div>
                                
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-walnut-400 hover:text-red-600 transition" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Clear Cart -->
                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('catalog') }}" class="text-[0.65rem] uppercase tracking-widest text-muted hover:text-walnut-950 font-bold transition">
                        Lanjut Jelajah
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan keranjang belanja?')">
                        @csrf
                        <button type="submit" class="text-[0.65rem] uppercase tracking-widest text-walnut-400 hover:text-red-600 font-bold transition">
                            Kosongkan Keranjang
                        </button>
                    </form>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="space-y-6 lg:sticky lg:top-32">
                <div class="bg-cream-50 border border-walnut-800/10 p-8 space-y-8">
                    <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950 pb-6 border-b border-walnut-800/10">Ringkasan</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between text-[0.7rem] uppercase tracking-widest text-muted font-bold">
                            <span>Subtotal</span>
                            <span class="text-walnut-950">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-[0.7rem] uppercase tracking-widest text-muted font-bold">
                            <span>Pengiriman</span>
                            <span class="text-[0.55rem] text-walnut-400">Dihitung saat checkout</span>
                        </div>
                        
                        <div class="pt-6 border-t border-walnut-800/10 flex justify-between items-end">
                            <span class="text-[0.65rem] uppercase tracking-widest text-walnut-950 font-bold">Total</span>
                            <span class="text-2xl font-display font-black text-walnut-950 tracking-tight">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" 
                        class="w-full block py-4 bg-walnut-900 text-gold-500 text-center font-bold uppercase text-[0.7rem] tracking-[0.2em] hover:bg-gold-600 hover:text-white transition duration-500">
                        Proses Checkout
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
