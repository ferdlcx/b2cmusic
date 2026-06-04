<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan ({{ $startDate }} - {{ $endDate }})</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .header h2 {
            margin: 0;
            color: #1e293b;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #64748b;
            font-size: 10px;
        }
        .summary-box {
            margin-bottom: 25px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }
        .summary-title {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 8px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: 900;
            color: #4f46e5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 5px;
        }
        td {
            padding: 8px 5px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Penjualan</h2>
        <p>
            Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong><br>
            DjudasMS
        </p>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border-bottom: 0; padding: 0;">
                    <div class="summary-title">Total Pendapatan</div>
                    <div class="summary-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                </td>
                <td style="width: 50%; border-bottom: 0; padding: 0;">
                    <div class="summary-title">Total Pesanan Selesai / Dibayar</div>
                    <div class="summary-value">{{ $orders->count() }} <span style="font-size: 10px; font-weight: normal; color: #64748b;">transaksi</span></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Kode Pesanan</th>
                <th style="width: 18%;">Tanggal</th>
                <th style="width: 22%;">Pelanggan</th>
                <th style="width: 10%; text-align: right;">Subtotal</th>
                <th style="width: 10%; text-align: right;">Ongkir</th>
                <th style="width: 10%; text-align: right;">Diskon</th>
                <th style="width: 10%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $order)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>#{{ $order->order_code }}</strong></td>
                    <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $order->discount > 0 ? 'Rp ' . number_format($order->discount, 0, ',', '.') : '-' }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Laporan ini dihasilkan secara otomatis oleh sistem DjudasMS pada {{ now()->format('d M Y H:i:s') }} WIB.
    </div>
</body>
</html>
