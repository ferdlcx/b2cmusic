@extends('admin.layouts.admin')

@section('title', 'Kelola Kategori - Admin MusicStore Luxe')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Katalog Produk</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Kelola Kategori</h1>
            <p class="text-xs text-slate-500">Kelola kategori produk dan subkategori instrumen musik.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 rounded-xl text-xs font-semibold uppercase tracking-wider text-white hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/10 transition duration-300">
            <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Tambah Kategori
        </a>
    </div>

    <!-- Table -->
    @if($categories->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-12 text-center text-slate-500">
            <i data-lucide="layers" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
            <p class="text-xs font-semibold">Belum ada kategori ditambahkan.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Kategori Induk</th>
                        <th class="px-6 py-4">Jumlah Produk</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                            <td class="px-6 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-50 rounded-lg overflow-hidden shrink-0 border border-slate-100">
                                    @if($category->image)
                                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <i data-lucide="folder" class="w-5 h-5"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $category->name }}</span>
                                    <span class="text-[0.65rem] text-slate-400 font-mono">slug: {{ $category->slug }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 font-semibold text-xs">
                                {{ $category->parent ? $category->parent->name : '-' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $category->products_count }} Produk</td>
                            <td class="px-6 py-4 text-center">
                                @if($category->status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-emerald-50 text-emerald-700 border border-emerald-250 uppercase">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-slate-50 text-slate-500 border border-slate-200 uppercase">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-xs font-bold text-indigo-650 hover:underline">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
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
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
