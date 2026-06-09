@extends('admin.layouts.admin')

@section('title', 'Produk Terhapus - Admin DjudasMS')

@section('admin_content')
<div class="space-y-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-walnut-800/10">
        <div class="space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.45em] text-red-600 font-bold block">Sampah Produk</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tighter text-walnut-950">Produk Terhapus.</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products') }}" class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-500 hover:text-walnut-950 transition">← Katalog Aktif</a>
        </div>
    </div>

    <!-- Trashed Products Table -->
    @if($products->isEmpty())
        <div class="bg-cream-50 border border-walnut-800/10 p-16 text-center text-muted font-medium">
            Tempat sampah kosong. Tidak ada produk yang dihapus.
        </div>
    @else
        <div class="overflow-x-auto border border-walnut-800/10 bg-cream-50">
            <table class="w-full text-left">
                <thead class="text-[0.65rem] uppercase tracking-widest text-muted border-b border-walnut-800/10">
                    <tr>
                        <th class="px-6 py-4">Gambar</th>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Harga</th>
                        <th class="px-6 py-4">Dihapus Pada</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-100 transition">
                            <!-- Image -->
                            <td class="px-6 py-4">
                                <div class="w-14 h-14 bg-cream-100 border border-walnut-800/10 flex items-center justify-center p-1">
                                    @if($product->primaryImage)
                                        <img src="{{ $product->primaryImage->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover mix-blend-multiply" />
                                    @else
                                        <i data-lucide="image" class="w-5 h-5 text-walnut-800/20"></i>
                                    @endif
                                </div>
                            </td>

                            <!-- Name -->
                            <td class="px-6 py-5">
                                <span class="font-display text-[0.8rem] font-black uppercase tracking-tight text-walnut-950 block">{{ $product->name }}</span>
                                <span class="text-[0.65rem] text-walnut-600 font-mono">SKU: {{ $product->sku }}</span>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-5 text-[0.65rem] text-muted font-bold uppercase tracking-widest">
                                {{ $product->category ? $product->category->name : '-' }}
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-5 font-bold text-walnut-950 text-sm">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            <!-- Deleted At -->
                            <td class="px-6 py-5 text-[0.7rem] font-medium text-walnut-600">
                                {{ $product->deleted_at->format('d M Y, H:i') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex gap-4">
                                    <form action="{{ route('admin.products.restore', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[0.65rem] uppercase tracking-widest font-bold text-gold-600 hover:text-walnut-950 transition">Pulihkan</button>
                                    </form>
                                    
                                    <form action="{{ route('admin.products.forceDelete', $product->id) }}" method="POST" onsubmit="return confirm('PENGHAPUSAN PERMANEN: Apakah Anda yakin ingin menghapus produk ini dari database secara total? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[0.65rem] uppercase tracking-widest font-bold text-red-600 hover:text-red-800 transition">Hapus Permanen</button>
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
