@extends('admin.layouts.admin')

@section('title', 'Laporan Pelanggan - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-6">
        <span class="text-xs uppercase tracking-[0.45em] text-muted font-bold">Laporan & Audit</span>
        <h1 class="text-3xl font-black uppercase tracking-tight text-walnut-950 mt-2">Laporan Pelanggan</h1>
        <p class="text-xs text-muted font-normal">Analisis pelanggan Anda dengan nilai pembelian tertinggi (Top Spenders).</p>
    </div>

    <!-- Top Customers Table Card -->
    <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-6 md:p-8 shadow-sm space-y-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-walnut-800 flex items-center gap-2">
            <i data-lucide="award" class="w-4 h-4 text-gold-600"></i> 15 Pelanggan dengan Pembelian Tertinggi
        </h3>

        @if($topCustomers->isEmpty())
            <p class="text-xs text-walnut-400 font-semibold text-center py-10">Belum ada transaksi tuntas.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase tracking-widest text-walnut-400 bg-cream-100 border-b border-walnut-800/10">
                        <tr>
                            <th class="px-5 py-3 text-center" style="width: 8%;">Peringkat</th>
                            <th class="px-5 py-3">Nama Pelanggan</th>
                            <th class="px-5 py-3">Email</th>
                            <th class="px-5 py-3 text-center">Jumlah Pesanan</th>
                            <th class="px-5 py-3 text-right">Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCustomers as $index => $customer)
                            <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-100/50">
                                <td class="px-5 py-4 text-center font-display font-black text-walnut-900 text-xs">
                                    #{{ $index + 1 }}
                                </td>
                                <td class="px-5 py-4 font-bold text-walnut-900 text-xs">{{ $customer->name }}</td>
                                <td class="px-5 py-4 text-muted text-xs font-semibold">{{ $customer->email }}</td>
                                <td class="px-5 py-4 text-center font-bold text-walnut-800 text-xs">{{ $customer->orders_count }} Pesanan</td>
                                <td class="px-5 py-4 text-right font-black text-gold-600 text-xs">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
