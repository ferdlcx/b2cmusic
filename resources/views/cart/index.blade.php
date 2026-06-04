@extends('layouts.app')

@section('title', 'Keranjang Belanja - MusicStore Luxe')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-200/60 pb-8 space-y-2">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Kantong Belanja</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tight text-slate-950">Keranjang Belanja</h1>
    </div>

    @if($cartItems->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[36px] p-16 text-center text-slate-500 shadow-sm space-y-5">
            <div class="w-16 h-16 bg-slate-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                <i data-lucide="shopping-cart" class="w-8 h-8"></i>
            </div>
            <div class="space-y-1">
                <p class="font-display text-lg font-bold text-slate-900 uppercase">Keranjang Belanja Kosong</p>
                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">Silakan tambahkan beberapa instrumen atau vinyl menarik dari katalog produk kami.</p>
            </div>
            <div class="pt-2">
                <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 text-white rounded-2xl text-xs font-semibold tracking-wider hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i> Mulai Belanja
                </a>
            </div>
        </div>
    @else
        <div class="grid gap-10 lg:grid-cols-[1fr_380px]">
            <!-- Items List -->
            <div class="space-y-6">
                <!-- Desktop Item List (Hidden on mobile) -->
                <div class="hidden sm:block overflow-hidden rounded-[32px] border border-slate-200/80 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-200/60">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Produk</th>
                                    <th class="px-6 py-4 text-center font-bold">Jumlah</th>
                                    <th class="px-6 py-4 text-right font-bold">Subtotal</th>
                                    <th class="px-6 py-4 text-center font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/40 transition">
                                        <!-- Product Info -->
                                        <td class="px-6 py-5 flex items-center gap-4">
                                            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/60 flex-shrink-0 flex items-center justify-center">
                                                <img src="{{ $item->product->primaryImage ? $item->product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=80&q=80' }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover" />
                                            </div>
                                            <div class="space-y-1">
                                                <a href="{{ route('products.show', $item->product->slug) }}" class="font-display font-bold text-slate-900 hover:text-indigo-600 transition line-clamp-1 text-sm uppercase tracking-tight">{{ $item->product->name }}</a>
                                                <span class="text-xs text-slate-400 font-semibold block">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                            </div>
                                        </td>

                                        <!-- Quantity Update Form -->
                                        <td class="px-6 py-5 text-center">
                                            <div class="inline-block">
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" x-data="{ qty: {{ $item->quantity }} }" class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50 w-28 shadow-sm">
                                                    @csrf
                                                    <button type="button" @click="if (qty > 1) { qty--; $nextTick(() => $el.form.submit()) }" class="px-3 py-2 hover:bg-slate-100 text-slate-500 font-black focus:outline-none transition">-</button>
                                                    <input type="number" name="quantity" x-model="qty" readonly class="w-full bg-transparent text-center text-xs font-black text-slate-800 focus:outline-none" />
                                                    <button type="button" @click="if (qty < {{ $item->product->stock }}) { qty++; $nextTick(() => $el.form.submit()) }" class="px-3 py-2 hover:bg-slate-100 text-slate-500 font-black focus:outline-none transition">+</button>
                                                </form>
                                            </div>
                                        </td>

                                        <!-- Subtotal -->
                                        <td class="px-6 py-5 text-right font-black text-slate-900">
                                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                        </td>

                                        <!-- Remove Item -->
                                        <td class="px-6 py-5 text-center">
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 hover:text-rose-700 rounded-xl transition duration-300" title="Hapus Produk">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Item Cards (Only on mobile screens) -->
                <div class="sm:hidden space-y-4">
                    @foreach($cartItems as $item)
                        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/60 flex-shrink-0 flex items-center justify-center">
                                    <img src="{{ $item->product->primaryImage ? $item->product->primaryImage->image : 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=80&q=80' }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover" />
                                </div>
                                <div class="min-w-0 flex-1 space-y-1">
                                    <span class="text-[0.65rem] uppercase tracking-wider text-slate-400 font-bold block">{{ $item->product->category->name }}</span>
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="font-display font-bold text-slate-900 hover:text-indigo-600 transition line-clamp-2 text-xs uppercase tracking-tight">{{ $item->product->name }}</a>
                                    <span class="text-xs text-slate-500 font-semibold block">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-slate-100 gap-2">
                                <!-- Quantity Controls -->
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" x-data="{ qty: {{ $item->quantity }} }" class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50 w-24 shadow-sm">
                                    @csrf
                                    <button type="button" @click="if (qty > 1) { qty--; $nextTick(() => $el.form.submit()) }" class="px-2.5 py-1.5 hover:bg-slate-100 text-slate-500 font-black focus:outline-none transition">-</button>
                                    <input type="number" name="quantity" x-model="qty" readonly class="w-full bg-transparent text-center text-xs font-black text-slate-800 focus:outline-none" />
                                    <button type="button" @click="if (qty < {{ $item->product->stock }}) { qty++; $nextTick(() => $el.form.submit()) }" class="px-2.5 py-1.5 hover:bg-slate-100 text-slate-500 font-black focus:outline-none transition">+</button>
                                </form>
                                
                                <div class="text-right flex-1 pr-2">
                                    <span class="text-[0.65rem] uppercase tracking-wider text-slate-400 font-bold block">Subtotal</span>
                                    <span class="text-xs font-black text-slate-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                </div>
                                
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 hover:text-rose-700 rounded-xl transition duration-300" title="Hapus Produk">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Clear Cart & Continue -->
                <div class="flex items-center justify-between flex-wrap gap-4 pt-2">
                    <a href="{{ route('catalog') }}" class="text-xs uppercase tracking-widest text-slate-500 hover:text-slate-900 font-bold flex items-center gap-1.5 transition">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali Belanja
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan keranjang belanja?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 text-xs uppercase tracking-widest text-rose-600 hover:text-rose-800 font-bold transition">
                            <i data-lucide="x-circle" class="w-4 h-4"></i> Kosongkan Keranjang
                        </button>
                    </form>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="space-y-6">
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-6">
                    <h3 class="font-display text-lg font-black uppercase tracking-tight text-slate-950 pb-4 border-b border-slate-100">Ringkasan Belanja</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between text-xs text-slate-500 font-semibold">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-slate-500 font-semibold">
                            <span>Pengiriman</span>
                            <span class="text-[0.65rem] uppercase tracking-wider text-slate-400 font-bold">Dihitung di checkout</span>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-100 flex justify-between items-end">
                            <span class="text-xs font-bold text-slate-900">Total Sementara</span>
                            <span class="text-2xl font-black text-indigo-600">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" 
                        class="w-full block py-4.5 bg-indigo-600 text-white rounded-2xl text-center font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300">
                        Lanjut ke Checkout
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
