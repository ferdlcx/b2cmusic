@extends('layouts.app')

@section('title', 'Keranjang Belanja - MusicStore Luxe')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-8">
        <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Kantong Belanja</span>
        <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Keranjang Belanja</h1>
    </div>

    @if($cartItems->isEmpty())
        <div class="bg-white border border-slate-200 rounded-[40px] p-16 text-center text-slate-500 shadow-[0_30px_80px_rgba(15,23,42,0.04)]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mx-auto mb-4 text-slate-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <p class="text-lg font-bold text-slate-900">Keranjang Belanja Anda Kosong</p>
            <p class="text-sm mt-1 text-slate-400">Silakan tambahkan beberapa instrumen atau vinyl menarik dari katalog kami.</p>
            <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center px-6 py-3 bg-slate-950 text-white uppercase text-xs tracking-[0.2em] rounded-xl hover:bg-slate-800 transition mt-6">Mulai Belanja</a>
        </div>
    @else
        <div class="grid gap-10 lg:grid-cols-[1fr_380px]">
            <!-- Items List -->
            <div class="space-y-6">
                <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Produk</th>
                                <th class="px-6 py-4 text-center">Jumlah</th>
                                <th class="px-6 py-4 text-right">Subtotal</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                                    <!-- Product Info -->
                                    <td class="px-6 py-6 flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center">
                                            <img src="{{ $item->product->primaryImage ? $item->product->primaryImage->image : 'https://placehold.co/100' }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover" />
                                        </div>
                                        <div>
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-bold text-slate-900 hover:underline line-clamp-1 text-sm uppercase tracking-tight">{{ $item->product->name }}</a>
                                            <span class="text-xs text-slate-400 block mt-1">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                        </div>
                                    </td>

                                    <!-- Quantity Update Form -->
                                    <td class="px-6 py-6 text-center">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="inline-flex items-center gap-2">
                                            @csrf
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" 
                                                class="w-14 px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-center text-xs font-bold focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white" />
                                            <button type="submit" class="text-xs font-bold text-slate-950 hover:text-slate-600 uppercase tracking-widest px-2 py-1">Update</button>
                                        </form>
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="px-6 py-6 text-right font-black text-slate-900">
                                        Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </td>

                                    <!-- Remove Item -->
                                    <td class="px-6 py-6 text-center">
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mx-auto">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Clear Cart -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('catalog') }}" class="text-xs uppercase tracking-widest text-slate-500 font-bold hover:text-slate-900 transition">← Kembali Belanja</a>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan keranjang?')">
                        @csrf
                        <button type="submit" class="text-xs uppercase tracking-widest text-rose-600 font-bold hover:text-rose-800 transition">Kosongkan Keranjang</button>
                    </form>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="space-y-6">
                <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-6">
                    <h3 class="text-xl font-black uppercase tracking-tight text-slate-950 pb-4 border-b border-slate-100">Ringkasan Belanja</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-slate-500">
                            <span>Pengiriman</span>
                            <span class="text-xs uppercase tracking-wider text-slate-400">Dihitung di checkout</span>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-100 flex justify-between items-end">
                            <span class="text-sm font-bold text-slate-900">Total Sementara</span>
                            <span class="text-2xl font-black text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" 
                        class="w-full block py-4 bg-slate-950 text-white rounded-xl text-center font-black uppercase text-xs tracking-[0.22em] hover:bg-slate-800 transition shadow-sm">
                        Lanjut ke Checkout
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
