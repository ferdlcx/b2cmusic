@extends('admin.layouts.admin')

@section('title', 'Kelola Flash Sale - Admin MusicStore Luxe')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Pemasaran</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Kelola Flash Sale</h1>
            <p class="text-xs text-slate-500">Kelola promosi flash sale berbatas waktu untuk instrumen musik.</p>
        </div>
        <a href="{{ route('admin.flashSales.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 rounded-xl text-xs font-semibold uppercase tracking-wider text-white hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/10 transition duration-300">
            <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Tambah Flash Sale
        </a>
    </div>

    <!-- Table -->
    @if($flashSales->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-12 text-center text-slate-500">
            <i data-lucide="zap" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
            <p class="text-xs font-semibold">Belum ada promo flash sale ditambahkan.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Nama Promo</th>
                        <th class="px-6 py-4">Waktu Mulai</th>
                        <th class="px-6 py-4">Waktu Selesai</th>
                        <th class="px-6 py-4">Jumlah Produk</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($flashSales as $sale)
                        <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $sale->name }}</td>
                            <td class="px-6 py-4 text-slate-650 font-semibold text-xs">
                                {{ $sale->start_time->format('d M Y, H:i') }} WIB
                            </td>
                            <td class="px-6 py-4 text-slate-650 font-semibold text-xs">
                                {{ $sale->end_time->format('d M Y, H:i') }} WIB
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700">
                                {{ $sale->items_count }} Produk
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $now = now();
                                    $isActive = $sale->status && $sale->start_time <= $now && $sale->end_time >= $now;
                                @endphp
                                @if($isActive)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-amber-50 text-amber-700 border border-amber-255 uppercase animate-pulse">Berlangsung</span>
                                @elseif($sale->status && $sale->start_time > $now)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase">Mendatang</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-slate-50 text-slate-500 border border-slate-200 uppercase">Berakhir / Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <a href="{{ route('admin.flashSales.edit', $sale->id) }}" class="text-xs font-bold text-indigo-650 hover:underline">Edit</a>
                                <form action="{{ route('admin.flashSales.destroy', $sale->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus flash sale ini?')">
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
            {{ $flashSales->links() }}
        </div>
    @endif
</div>
@endsection
