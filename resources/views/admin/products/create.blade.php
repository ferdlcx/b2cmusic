@extends('layouts.app')

@section('title', 'Tambah Produk Baru - Admin MusicStore')

@section('content')
<div class="max-w-4xl mx-auto space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-8 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Produk Baru</span>
            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Tambah Produk</h1>
        </div>
        <a href="{{ route('admin.products') }}" class="text-xs uppercase tracking-widest font-bold text-slate-500 hover:text-slate-950 transition">← Kembali</a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-[32px] p-8 md:p-10 shadow-sm space-y-8">
        @csrf
        
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Product Name -->
            <div class="space-y-2">
                <label for="name" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Nama Produk</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('name') border-rose-500 @enderror" 
                    placeholder="e.g. Fender Stratocaster Player Series" />
                @error('name')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Category -->
            <div class="space-y-2">
                <label for="category_id" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Kategori</label>
                <select name="category_id" id="category_id" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('category_id') border-rose-500 @enderror">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Brand -->
            <div class="space-y-2">
                <label for="brand" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Merek / Brand</label>
                <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm" 
                    placeholder="e.g. Fender" />
            </div>

            <!-- SKU -->
            <div class="space-y-2">
                <label for="sku" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">SKU (Kode Unik)</label>
                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('sku') border-rose-500 @enderror" 
                    placeholder="e.g. GTR-FEN-STRAT" />
                @error('sku')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Price -->
            <div class="space-y-2">
                <label for="price" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Harga (Rupiah)</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('price') border-rose-500 @enderror" 
                    placeholder="e.g. 14500000" />
                @error('price')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Stock -->
            <div class="space-y-2">
                <label for="stock" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Jumlah Stok</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', 1) }}" required min="0"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('stock') border-rose-500 @enderror" 
                    placeholder="e.g. 5" />
                @error('stock')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Short Description -->
        <div class="space-y-2">
            <label for="short_description" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Deskripsi Singkat</label>
            <input type="text" name="short_description" id="short_description" value="{{ old('short_description') }}"
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm" 
                placeholder="Penjelasan ringkas produk untuk list katalog." />
        </div>

        <!-- Full Description -->
        <div class="space-y-2">
            <label for="description" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Deskripsi Lengkap</label>
            <textarea name="description" id="description" rows="5"
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm" 
                placeholder="Detail spesifikasi, keunggulan, material, dan cerita produk secara lengkap.">{{ old('description') }}</textarea>
        </div>

        <!-- Primary Image Upload -->
        <div class="space-y-2">
            <label for="image" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Gambar Utama Produk</label>
            <input type="file" name="image" id="image" required
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('image') border-rose-500 @enderror" />
            @error('image')
                <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Dynamic Specifications Panel -->
        <div class="space-y-4 border-t border-slate-100 pt-6">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-widest text-slate-500 font-bold">Spesifikasi Tambahan (ERD-aligned)</span>
                <button type="button" id="add-spec-btn" 
                    class="text-xs font-bold text-slate-950 border border-slate-900 px-3 py-1.5 rounded-lg hover:bg-slate-900 hover:text-white transition">
                    + Tambah Baris
                </button>
            </div>
            
            <div id="specs-container" class="space-y-3">
                <!-- Stdin rows appended here -->
                <div class="flex gap-3 spec-row">
                    <input type="text" name="spec_names[]" placeholder="Nama Spesifikasi (e.g. Bahan Body)" 
                        class="w-1/3 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white" />
                    <input type="text" name="spec_values[]" placeholder="Nilai Spesifikasi (e.g. Alder Wood)" 
                        class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white" />
                    <button type="button" class="remove-spec-btn text-rose-600 font-bold px-3 hover:text-rose-800 transition">X</button>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
            class="w-full py-4 bg-slate-950 text-white rounded-2xl font-black uppercase text-xs tracking-[0.25em] hover:bg-slate-800 transition pt-4">
            Simpan Produk
        </button>
    </form>
</div>

<!-- Script to handle dynamic spec fields -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('specs-container');
        const addBtn = document.getElementById('add-spec-btn');

        addBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'flex gap-3 spec-row';
            newRow.innerHTML = `
                <input type="text" name="spec_names[]" placeholder="Nama Spesifikasi" 
                    class="w-1/3 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white" />
                <input type="text" name="spec_values[]" placeholder="Nilai Spesifikasi" 
                    class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white" />
                <button type="button" class="remove-spec-btn text-rose-600 font-bold px-3 hover:text-rose-800 transition">X</button>
            `;
            container.appendChild(newRow);
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-spec-btn')) {
                e.target.parentElement.remove();
            }
        });
    });
</script>
@endsection
