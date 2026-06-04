@extends('admin.layouts.admin')

@section('title', 'Produk Terhapus - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Katalog Produk</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Tempat Sampah Produk</h1>
            <p class="text-xs text-slate-500">Daftar produk yang telah dihapus sementara (soft delete).</p>
        </div>
        <a href="{{ route('admin.products') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-xs font-semibold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2 text-slate-400"></i> Katalog Aktif
        </a>
    </div>

    <!-- Trashed Products Table -->
    @if($products->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-12 text-center text-slate-500">
            <i data-lucide="trash-2" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
            <p class="text-xs font-semibold">Tempat sampah kosong.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Harga</th>
                        <th class="px-6 py-4">Dihapus Pada</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                            <td class="px-6 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-50 rounded-lg overflow-hidden shrink-0 border border-slate-100">
                                    @if($product->primaryImage)
                                        <img src="{{ $product->primaryImage->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <i data-lucide="image" class="w-5 h-5"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $product->name }}</span>
                                    <span class="text-[0.65rem] text-slate-400 font-mono">SKU: {{ $product->sku }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">{{ $product->category ? $product->category->name : '-' }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $product->deleted_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs uppercase tracking-wider font-black text-indigo-600 hover:text-indigo-700 transition">Pulihkan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
