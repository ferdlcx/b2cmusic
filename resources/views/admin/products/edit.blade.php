@extends('admin.layouts.admin')

@section('title', 'Edit Produk: ' . $product->name . ' - Admin DjudasMS')

@section('admin_content')
<div class="max-w-4xl mx-auto space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-8 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-muted font-bold">Menu Edit</span>
            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-walnut-950 mt-3">Edit Produk</h1>
        </div>
        <a href="{{ route('admin.products') }}" class="text-xs uppercase tracking-widest font-bold text-muted hover:text-walnut-950 transition">← Kembali</a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-8 md:p-10 shadow-sm space-y-8">
        @csrf
        @method('PUT')
        
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Product Name -->
            <div class="space-y-2">
                <label for="name" class="text-xs uppercase tracking-widest text-muted font-bold block">Nama Produk</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-sm @error('name') border-rose-500 @enderror" />
                @error('name')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Category -->
            <div class="space-y-2">
                <label for="category_id" class="text-xs uppercase tracking-widest text-muted font-bold block">Kategori</label>
                <select name="category_id" id="category_id" required
                    class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-sm">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Brand -->
            <div class="space-y-2">
                <label for="brand" class="text-xs uppercase tracking-widest text-muted font-bold block">Merek / Brand</label>
                <input type="text" name="brand" id="brand" value="{{ old('brand', $product->brand) }}"
                    class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-sm" />
            </div>

            <!-- SKU -->
            <div class="space-y-2">
                <label for="sku" class="text-xs uppercase tracking-widest text-muted font-bold block">SKU (Kode Unik)</label>
                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required
                    class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-sm @error('sku') border-rose-500 @enderror" />
                @error('sku')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Price -->
            <div class="space-y-2">
                <label for="price" class="text-xs uppercase tracking-widest text-muted font-bold block">Harga (Rupiah)</label>
                <input type="number" name="price" id="price" value="{{ old('price', (int)$product->price) }}" required min="0"
                    class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-sm @error('price') border-rose-500 @enderror" />
                @error('price')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Stock -->
            <div class="space-y-2">
                <label for="stock" class="text-xs uppercase tracking-widest text-muted font-bold block">Jumlah Stok</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                    class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-sm @error('stock') border-rose-500 @enderror" />
                @error('stock')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Short Description -->
        <div class="space-y-2">
            <label for="short_description" class="text-xs uppercase tracking-widest text-muted font-bold block">Deskripsi Singkat</label>
            <input type="text" name="short_description" id="short_description" value="{{ old('short_description', $product->short_description) }}"
                class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-sm" />
        </div>

        <!-- Full Description -->
        <div class="space-y-2">
            <label for="description" class="text-xs uppercase tracking-widest text-muted font-bold block">Deskripsi Lengkap</label>
            <textarea name="description" id="description" rows="5"
                class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-sm">{{ old('description', $product->description) }}</textarea>
        </div>

        <!-- Current & New Image Upload -->
        <div class="grid gap-6 md:grid-cols-[150px_1fr]">
            <div>
                <span class="text-xs uppercase tracking-widest text-muted font-bold block mb-2">Gambar Saat Ini</span>
                <div class="w-full h-32 rounded-2xl overflow-hidden bg-cream-100 border border-walnut-800/10 flex items-center justify-center">
                    <img src="{{ $product->primaryImage ? $product->primaryImage->image : 'https://placehold.co/150' }}" alt="Current Image" class="w-full h-full object-cover" />
                </div>
            </div>
            <div class="space-y-2 flex flex-col justify-end">
                <label for="image" class="text-xs uppercase tracking-widest text-muted font-bold block">Ganti Gambar (Opsional)</label>
                <input type="file" name="image" id="image"
                    class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-sm" />
                @error('image')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Dynamic Specifications Panel -->
        <div class="space-y-4 border-t border-walnut-800/10 pt-6">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-widest text-muted font-bold">Spesifikasi Tambahan (ERD-aligned)</span>
                <button type="button" id="add-spec-btn" 
                    class="text-xs font-bold text-walnut-950 border border-walnut-900 px-3 py-1.5 rounded-lg hover:bg-walnut-900 hover:text-white transition">
                    + Tambah Baris
                </button>
            </div>
            
            <div id="specs-container" class="space-y-3">
                @forelse($product->specifications as $spec)
                    <div class="flex gap-3 spec-row">
                        <input type="text" name="spec_names[]" value="{{ $spec->spec_name }}" placeholder="Nama Spesifikasi" 
                            class="w-1/3 px-4 py-2.5 bg-cream-100 border border-walnut-800/10 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50" />
                        <input type="text" name="spec_values[]" value="{{ $spec->spec_value }}" placeholder="Nilai Spesifikasi" 
                            class="flex-1 px-4 py-2.5 bg-cream-100 border border-walnut-800/10 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50" />
                        <button type="button" class="remove-spec-btn text-rose-600 font-bold px-3 hover:text-rose-800 transition">X</button>
                    </div>
                @empty
                    <div class="flex gap-3 spec-row">
                        <input type="text" name="spec_names[]" placeholder="Nama Spesifikasi" 
                            class="w-1/3 px-4 py-2.5 bg-cream-100 border border-walnut-800/10 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50" />
                        <input type="text" name="spec_values[]" placeholder="Nilai Spesifikasi" 
                            class="flex-1 px-4 py-2.5 bg-cream-100 border border-walnut-800/10 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50" />
                        <button type="button" class="remove-spec-btn text-rose-600 font-bold px-3 hover:text-rose-800 transition">X</button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
            class="w-full py-4 bg-walnut-950 text-white rounded-2xl font-black uppercase text-xs tracking-[0.25em] hover:bg-walnut-800 transition">
            Perbarui Produk
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
                    class="w-1/3 px-4 py-2.5 bg-cream-100 border border-walnut-800/10 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50" />
                <input type="text" name="spec_values[]" placeholder="Nilai Spesifikasi" 
                    class="flex-1 px-4 py-2.5 bg-cream-100 border border-walnut-800/10 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50" />
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
