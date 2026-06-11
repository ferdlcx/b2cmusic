@extends('admin.layouts.admin')

@section('title', 'Tambah Kategori - Admin DjudasMS')

@section('admin_content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-muted font-bold">Katalog Kategori</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Tambah Kategori</h1>
        </div>
        <a href="{{ route('admin.categories') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-walnut-800/10 bg-cream-50 rounded-xl text-xs font-semibold uppercase tracking-wider text-walnut-800 hover:bg-cream-100 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2 text-slate-400"></i> Batal
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-8 shadow-sm">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Nama Kategori</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-5 py-4 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-xs font-semibold @error('name') border-rose-500 @enderror" 
                    placeholder="Contoh: Gitar Listrik" />
                @error('name')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Parent Category -->
            <div class="space-y-1.5">
                <label for="parent_id" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kategori Induk (Subkategori)</label>
                <select name="parent_id" id="parent_id"
                    class="w-full px-5 py-4 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-xs font-semibold @error('parent_id') border-rose-500 @enderror">
                    <option value="">-- Tanpa Induk (Kategori Utama) --</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
                <p class="text-[0.65rem] text-slate-400 font-medium">Pilih kategori induk jika kategori ini merupakan subkategori.</p>
                @error('parent_id')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label for="description" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Deskripsi</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-5 py-4 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-xs font-semibold leading-relaxed @error('description') border-rose-500 @enderror" 
                    placeholder="Deskripsi singkat mengenai kategori..."></textarea>
                @error('description')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Status & Image Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status -->
                <div class="space-y-1.5">
                    <label for="status" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Status</label>
                    <select name="status" id="status" required
                        class="w-full px-5 py-4 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-xs font-semibold">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <!-- Image -->
                <div class="space-y-1.5">
                    <label for="image" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Gambar Cover</label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="w-full px-5 py-3.5 bg-cream-100 border border-walnut-800/10 rounded-2xl text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition text-xs font-semibold @error('image') border-rose-500 @enderror" />
                    @error('image')
                        <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Kategori
            </button>
        </form>
    </div>
</div>
@endsection
