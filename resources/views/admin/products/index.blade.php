@extends('admin.layouts.admin')

@section('title', 'Kelola Produk - Admin DjudasMS')

@section('admin_content')
<div class="space-y-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-walnut-800/10">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Katalog Inventaris</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Daftar Produk.</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-500 hover:text-walnut-950 transition">← Dashboard</a>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-5 py-3 bg-walnut-900 text-gold-500 text-[0.65rem] font-bold uppercase tracking-widest hover:bg-gold-600 hover:text-white transition duration-300">Tambah Produk</a>
        </div>
    </div>

    <!-- Products Table -->
    @if($products->isEmpty())
        <div class="bg-cream-50 border border-walnut-800/10 p-16 text-center text-muted font-medium">
            Katalog produk kosong. Klik "Tambah Produk" untuk mengunggah produk pertama Anda.
        </div>
    @else
        <div class="overflow-x-auto border border-walnut-800/10 bg-cream-50">
            <table class="w-full text-left">
                <thead class="text-[0.65rem] uppercase tracking-widest text-muted border-b border-walnut-800/10">
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
                        <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-100 transition">
                            <!-- Image -->
                            <td class="px-6 py-4">
                                <div class="w-14 h-14 bg-cream-100 border border-walnut-800/10 flex items-center justify-center p-1">
                                    <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://placehold.co/100' }}" alt="{{ $product->name }}" class="w-full h-full object-cover mix-blend-multiply" />
                                </div>
                            </td>

                            <!-- Name -->
                            <td class="px-6 py-5 font-display text-[0.8rem] font-black uppercase tracking-tight text-walnut-950">
                                {{ $product->name }}
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-5 text-[0.65rem] text-muted font-bold uppercase tracking-widest">
                                {{ $product->category->name }}
                            </td>

                            <!-- SKU -->
                            <td class="px-6 py-5 font-mono text-[0.7rem] text-walnut-600">
                                {{ $product->sku }}
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-5 font-bold text-walnut-950 text-sm">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            <!-- Stock -->
                            <td class="px-6 py-5 text-center">
                                @if($product->stock <= 0)
                                    <span class="text-[0.65rem] font-bold text-red-700 uppercase tracking-widest">Habis</span>
                                @elseif($product->stock <= 5)
                                    <span class="text-[0.65rem] font-bold text-gold-700 uppercase tracking-widest">{{ $product->stock }} (Tipis)</span>
                                @else
                                    <span class="text-[0.65rem] font-bold text-walnut-900 uppercase tracking-widest">{{ $product->stock }}</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex gap-4">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-[0.65rem] uppercase tracking-widest font-bold text-gold-600 hover:text-walnut-950 transition">Edit</a>
                                    
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[0.65rem] uppercase tracking-widest font-bold text-red-600 hover:text-red-800 transition">Hapus</button>
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
