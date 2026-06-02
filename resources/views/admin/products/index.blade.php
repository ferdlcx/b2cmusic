@extends('layouts.app')

@section('title', 'Kelola Produk - Admin MusicStore')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-8 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Menu Admin</span>
            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Daftar Produk</h1>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-xs uppercase tracking-widest font-bold text-slate-500 hover:text-slate-950 transition">← Dashboard</a>
            <a href="{{ route('admin.products.create') }}" class="text-xs uppercase tracking-widest font-black bg-slate-950 text-white px-5 py-3 rounded-xl hover:bg-slate-800 transition">Tambah Produk</a>
        </div>
    </div>

    <!-- Products Table -->
    @if($products->isEmpty())
        <div class="bg-white border border-slate-200 rounded-[32px] p-16 text-center text-slate-500">
            Katalog produk kosong. Klik "Tambah Produk" untuk mengunggah produk pertama Anda.
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Gambar</th>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">SKU</th>
                        <th class="px-6 py-4">Harga</th>
                        <th class="px-6 py-4 text-center">Stok</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                            <!-- Image -->
                            <td class="px-6 py-4">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center">
                                    <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://placehold.co/100' }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                </div>
                            </td>

                            <!-- Name -->
                            <td class="px-6 py-4 font-bold text-slate-900 uppercase tracking-tight">
                                {{ $product->name }}
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4 text-slate-500 font-semibold uppercase text-xs">
                                {{ $product->category->name }}
                            </td>

                            <!-- SKU -->
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                {{ $product->sku }}
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-4 font-bold text-slate-900">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            <!-- Stock -->
                            <td class="px-6 py-4 text-center">
                                @if($product->stock <= 0)
                                    <span class="text-rose-600 font-black text-xs uppercase">Habis</span>
                                @elseif($product->stock <= 5)
                                    <span class="text-amber-600 font-black text-xs">{{ $product->stock }} (Tipis)</span>
                                @else
                                    <span class="text-slate-800 font-bold text-xs">{{ $product->stock }}</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex gap-4">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-xs uppercase font-bold text-slate-600 hover:text-slate-950">Edit</a>
                                    
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs uppercase font-bold text-rose-600 hover:text-rose-800">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-6">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
