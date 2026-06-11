@extends('admin.layouts.admin')

@section('title', 'Laporan Penjualan - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-muted font-bold">Laporan & Audit</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-walnut-950 mt-2">Laporan Penjualan</h1>
            <p class="text-xs text-muted font-normal">Tinjau pendapatan, jumlah transaksi, dan unduh laporan penjualan.</p>
        </div>
        
        <!-- Export Action Buttons -->
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.export.sales.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-walnut-800/10 bg-cream-50 rounded-xl text-xs font-semibold uppercase tracking-wider text-walnut-800 hover:bg-cream-100 transition">
                <i data-lucide="file-text" class="w-4 h-4 mr-2 text-rose-500"></i> Ekspor PDF
            </a>
            <a href="{{ route('admin.reports.export.sales.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gold-600 rounded-xl text-xs font-semibold uppercase tracking-wider text-white hover:bg-gold-700 hover:shadow-lg hover:shadow-indigo-600/10 transition duration-300">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i> Ekspor CSV
            </a>
        </div>
    </div>

    <!-- Date Filter Form -->
    <div class="bg-cream-50 border border-walnut-800/10 rounded-[28px] p-6 shadow-sm">
        <form action="{{ route('admin.reports.sales') }}" method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="space-y-1.5 flex-1">
                <label for="start_date" class="text-[0.65rem] uppercase tracking-widest text-walnut-400 font-bold block">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                    class="w-full px-4 py-3 bg-cream-100 border border-walnut-800/10 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition" />
            </div>
            <div class="space-y-1.5 flex-1">
                <label for="end_date" class="text-[0.65rem] uppercase tracking-widest text-walnut-400 font-bold block">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                    class="w-full px-4 py-3 bg-cream-100 border border-walnut-800/10 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 transition" />
            </div>
            <button type="submit" class="px-6 py-3.5 bg-walnut-950 text-white rounded-xl text-xs font-semibold uppercase tracking-widest hover:bg-walnut-800 transition flex items-center justify-center gap-1.5">
                <i data-lucide="filter" class="w-4 h-4"></i> Filter
            </button>
        </form>
    </div>

    <!-- Metrics Cards -->
    <div class="grid gap-6 grid-cols-1 md:grid-cols-3">
        <!-- Revenue -->
        <div class="bg-cream-50 border border-walnut-800/10 rounded-[28px] p-6 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-gold-50 rounded-2xl flex items-center justify-center text-gold-600 shrink-0">
                <i data-lucide="wallet" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-walnut-400 font-bold">Total Pendapatan</p>
                <p class="text-xl font-black text-walnut-950 mt-0.5">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                <p class="text-[0.6rem] text-walnut-400 font-semibold mt-0.5">Selama periode terfilter</p>
            </div>
        </div>

        <!-- Orders Count -->
        <div class="bg-cream-50 border border-walnut-800/10 rounded-[28px] p-6 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shrink-0">
                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-walnut-400 font-bold">Pesanan Berhasil</p>
                <p class="text-xl font-black text-walnut-950 mt-0.5">{{ $ordersCount }} Transaksi</p>
                <p class="text-[0.6rem] text-walnut-400 font-semibold mt-0.5">Lunas / Sedang Diproses</p>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-cream-50 border border-walnut-800/10 rounded-[28px] p-6 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shrink-0">
                <i data-lucide="trending-up" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[0.65rem] uppercase tracking-widest text-walnut-400 font-bold">Rata-Rata Transaksi</p>
                <p class="text-xl font-black text-walnut-950 mt-0.5">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</p>
                <p class="text-[0.6rem] text-walnut-400 font-semibold mt-0.5">Per-Invoice belanja</p>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-6 md:p-8 shadow-sm space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-walnut-800">Tren Grafik Penjualan Harian</h3>
        <div class="h-80 w-full relative">
            <canvas id="salesChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-cream-50 border border-walnut-800/10 rounded-[32px] p-6 md:p-8 shadow-sm space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-walnut-800">Detail Pembelian Transaksi</h3>
        @if($orders->isEmpty())
            <p class="text-xs text-walnut-400 font-semibold text-center py-6">Belum ada transaksi terjadi.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase tracking-widest text-walnut-400 bg-cream-100 border-b border-walnut-800/10">
                        <tr>
                            <th class="px-5 py-3">Invoice</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Pelanggan</th>
                            <th class="px-5 py-3 text-right">Subtotal</th>
                            <th class="px-5 py-3 text-right">Ongkir</th>
                            <th class="px-5 py-3 text-right">Diskon</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-b border-walnut-800/10 last:border-0 hover:bg-cream-100/50">
                                <td class="px-5 py-4 font-bold text-walnut-900 uppercase">#{{ $order->order_code }}</td>
                                <td class="px-5 py-4 text-muted font-medium text-xs">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-5 py-4 text-walnut-800 font-semibold text-xs">{{ $order->user->name }}</td>
                                <td class="px-5 py-4 text-right text-walnut-800 font-medium text-xs">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right text-walnut-800 font-medium text-xs">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right text-rose-600 font-semibold text-xs">{{ $order->discount > 0 ? '-Rp ' . number_format($order->discount, 0, ',', '.') : '-' }}</td>
                                <td class="px-5 py-4 text-right font-black text-walnut-950 text-xs">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Prepare chart data from PHP variable
        const rawData = {!! json_encode($salesByDay) !!};
        const labels = rawData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });
        const dataValues = rawData.map(item => parseFloat(item.total));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan Harian (Rupiah)',
                    data: dataValues,
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: 'rgb(79, 70, 229)',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            },
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
