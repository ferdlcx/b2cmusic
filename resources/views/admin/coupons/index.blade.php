@extends('admin.layouts.admin')

@section('title', 'Kelola Kupon - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Pemasaran</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Kelola Kupon</h1>
            <p class="text-xs text-slate-500">Kelola kode promo dan kupon diskon belanja pelanggan.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 rounded-xl text-xs font-semibold uppercase tracking-wider text-white hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/10 transition duration-300">
            <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Tambah Kupon
        </a>
    </div>

    <!-- Table -->
    @if($coupons->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-12 text-center text-slate-500">
            <i data-lucide="ticket" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
            <p class="text-xs font-semibold">Belum ada kupon ditambahkan.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Kode Kupon</th>
                        <th class="px-6 py-4">Tipe & Nilai</th>
                        <th class="px-6 py-4">Min. Belanja</th>
                        <th class="px-6 py-4">Masa Berlaku</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                        <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <span class="inline-block font-mono font-black uppercase bg-slate-100 text-slate-900 border border-slate-200 rounded px-2.5 py-1 text-xs">
                                    {{ $coupon->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-900 font-bold text-xs">
                                @if($coupon->type === 'fixed')
                                    Rp {{ number_format($coupon->value, 0, ',', '.') }} (Potongan Tetap)
                                @else
                                    {{ intval($coupon->value) }}% (Persentase Diskon)
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-650 font-semibold text-xs">
                                Rp {{ number_format($coupon->min_purchase ?: 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                @if($coupon->start_date && $coupon->end_date)
                                    {{ $coupon->start_date->format('d M Y') }} s/d {{ $coupon->end_date->format('d M Y') }}
                                @elseif($coupon->end_date)
                                    S/d {{ $coupon->end_date->format('d M Y') }}
                                @else
                                    Selamanya
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($coupon->status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-emerald-50 text-emerald-700 border border-emerald-250 uppercase">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-slate-50 text-slate-500 border border-slate-200 uppercase">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="text-xs font-bold text-indigo-650 hover:underline">Edit</a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kupon ini?')">
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
            {{ $coupons->links() }}
        </div>
    @endif
</div>
@endsection
