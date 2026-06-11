@extends('admin.layouts.admin')

@section('title', 'Kelola Brand - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-muted font-bold">Katalog Produk</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-walnut-950 mt-2">Kelola Brand</h1>
            <p class="text-xs text-muted">Kelola brand atau produsen instrumen musik yang tersedia.</p>
        </div>
        <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gold-600 rounded-xl text-xs font-semibold uppercase tracking-wider text-white hover:bg-gold-700 hover:shadow-lg hover:shadow-indigo-600/10 transition duration-300">
            <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Tambah Brand
        </a>
    </div>

    <!-- Table -->
    @if($brands->isEmpty())
        <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-12 text-center text-muted">
            <i data-lucide="award" class="w-10 h-10 text-walnut-300 mx-auto mb-3"></i>
            <p class="text-xs font-semibold">Belum ada brand ditambahkan.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-walnut-800/10 bg-cream-50 shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-walnut-400 bg-cream-100 border-b border-walnut-800/10">
                    <tr>
                        <th class="px-6 py-4">Brand</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Jumlah Produk</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brands as $brand)
                        <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-100/50">
                            <td class="px-6 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 bg-cream-100 rounded-lg overflow-hidden shrink-0 border border-walnut-800/10 flex items-center justify-center p-1 bg-cream-50">
                                    @if($brand->logo)
                                        <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="w-full h-full object-contain" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-walnut-300">
                                            <i data-lucide="award" class="w-5 h-5"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold text-walnut-900 block">{{ $brand->name }}</span>
                                    <span class="text-[0.65rem] text-walnut-400 font-mono">slug: {{ $brand->slug }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-muted text-xs truncate max-w-xs">
                                {{ $brand->description ?: '-' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-walnut-800">{{ $brand->products_count }} Produk</td>
                            <td class="px-6 py-4 text-center">
                                @if($brand->status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-walnut-950/5 text-walnut-950 border border-walnut-800/20 uppercase">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-cream-100 text-muted border border-walnut-800/10 uppercase">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <a href="{{ route('admin.brands.edit', $brand->id) }}" class="text-xs font-bold text-gold-600 hover:underline">Edit</a>
                                <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus brand ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pt-4">
            {{ $brands->links() }}
        </div>
    @endif
</div>
@endsection
