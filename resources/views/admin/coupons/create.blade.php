@extends('admin.layouts.admin')

@section('title', 'Tambah Kupon - Admin DjudasMS')

@section('admin_content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Pemasaran</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Tambah Kupon</h1>
        </div>
        <a href="{{ route('admin.coupons') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-xs font-semibold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2 text-slate-400"></i> Batal
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm">
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Code -->
                <div class="space-y-1.5">
                    <label for="code" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kode Kupon</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('code') border-rose-500 @enderror" 
                        placeholder="Contoh: PROMOHEBOH" />
                    @error('code')
                        <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Type -->
                <div class="space-y-1.5">
                    <label for="type" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Tipe Kupon</label>
                    <select name="type" id="type" required
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold">
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Potongan Langsung (Rupiah)</option>
                        <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Persentase Diskon (%)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Value -->
                <div class="space-y-1.5">
                    <label for="value" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Nilai Diskon</label>
                    <input type="number" step="any" name="value" id="value" value="{{ old('value') }}" required
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('value') border-rose-500 @enderror" 
                        placeholder="Contoh: 50000 atau 10" />
                    @error('value')
                        <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Min Purchase -->
                <div class="space-y-1.5">
                    <label for="min_purchase" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Minimal Belanja (IDR)</label>
                    <input type="number" step="any" name="min_purchase" id="min_purchase" value="{{ old('min_purchase') }}"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold" 
                        placeholder="Opsional, misal: 100000" />
                </div>

                <!-- Max Discount -->
                <div class="space-y-1.5">
                    <label for="max_discount" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Maksimal Diskon (IDR)</label>
                    <input type="number" step="any" name="max_discount" id="max_discount" value="{{ old('max_discount') }}"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold" 
                        placeholder="Opsional (untuk persentase)" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div class="space-y-1.5">
                    <label for="start_date" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('start_date') border-rose-500 @enderror" />
                    @error('start_date')
                        <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- End Date -->
                <div class="space-y-1.5">
                    <label for="end_date" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('end_date') border-rose-500 @enderror" />
                    @error('end_date')
                        <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <div class="space-y-1.5">
                <label for="status" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Status</label>
                <select name="status" id="status" required
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold">
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Kupon
            </button>
        </form>
    </div>
</div>
@endsection
