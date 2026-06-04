@extends('admin.layouts.admin')

@section('title', 'Laporan Produk - Admin MusicStore Luxe')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6">
        <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Laporan & Audit</span>
        <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Laporan Produk</h1>
        <p class="text-xs text-slate-500 font-normal">Analisis produk terlaris Anda dan periksa daftar inventaris stok rendah.</p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        <!-- Top Selling Products -->
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 md:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i data-lucide="award" class="w-4 h-4 text-indigo-600"></i> 10 Produk Terlaris (Lunas)
            </h3>

            @if($topProducts->isEmpty())
                <p class="text-xs text-slate-400 font-semibold text-center py-10">Belum ada transaksi tuntas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3 text-center">Unit Terjual</th>
                                <th class="px-4 py-3 text-right">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $product)
                                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                                    <td class="px-4 py-4 flex items-center gap-3">
                                        <div class="w-8 h-8 bg-slate-50 border border-slate-100 rounded-lg overflow-hidden shrink-0">
                                            @if($product->primaryImage)
                                                <img src="{{ $product->primaryImage->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                    <i data-lucide="image" class="w-4 h-4"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-800 text-xs block truncate max-w-[150px]">{{ $product->name }}</span>
                                            <span class="text-[0.6rem] text-slate-400 font-mono">SKU: {{ $product->sku }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-indigo-650 text-xs">{{ $product->total_sold }} unit</td>
                                    <td class="px-4 py-4 text-right font-bold text-slate-900 text-xs">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 md:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i> Peringatan Stok Rendah (Stok &le; 5)
            </h3>

            @if($lowStockProducts->isEmpty())
                <div class="text-center py-10 bg-emerald-50/30 border border-emerald-100/50 rounded-2xl text-emerald-800">
                    <p class="text-xs font-semibold flex items-center justify-center gap-1.5"><i data-lucide="check" class="w-4 h-4 text-emerald-600"></i> Inventaris aman! Tidak ada stok rendah.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3 text-center">Stok</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                                    <td class="px-4 py-4 flex items-center gap-3">
                                        <div class="w-8 h-8 bg-slate-50 border border-slate-100 rounded-lg overflow-hidden shrink-0">
                                            @if($product->primaryImage)
                                                <img src="{{ $product->primaryImage->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                    <i data-lucide="image" class="w-4 h-4"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-800 text-xs block truncate max-w-[150px]">{{ $product->name }}</span>
                                            <span class="text-[0.6rem] text-slate-400 font-mono">SKU: {{ $product->sku }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-block text-[0.65rem] font-bold bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded">
                                            {{ $product->stock }} unit
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right font-bold text-slate-900 text-xs">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pt-4">
                    {{ $lowStockProducts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
