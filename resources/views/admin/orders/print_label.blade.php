<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Pengiriman - {{ $order->order_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .label-container {
            border: 2px dashed #000;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .tracking-number {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 10px 0;
        }
        .address-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .address-section {
            width: 48%;
        }
        .address-section h3 {
            margin-top: 0;
            font-size: 14px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .details {
            border-top: 2px solid #000;
            padding-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            text-align: left;
            padding: 5px 0;
            font-size: 14px;
        }
        th {
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .label-container {
                border: 1px solid #000;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div style="text-align: center; margin-bottom: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

    <div class="label-container">
        <div class="header">
            <h2>DjudasMS - Label Pengiriman</h2>
            <div class="tracking-number">
                {{ $order->shipment->tracking_number ?? 'RESI BELUM TERSEDIA' }}
            </div>
            <div>Kurir: <strong>{{ strtoupper($order->shipment->courier ?? 'KURIR') }}</strong></div>
            <div>Order ID: {{ $order->order_code }}</div>
        </div>

        <div class="address-box">
            <div class="address-section">
                <h3>Penerima</h3>
                <strong>{{ $order->address->recipient_name ?? $order->user->name }}</strong><br>
                {{ $order->address->phone ?? 'Tidak ada telepon' }}<br>
                {{ $order->address->address ?? '' }}<br>
                {{ $order->address->city ?? '' }}, {{ $order->address->province ?? '' }}<br>
                {{ $order->address->postal_code ?? '' }}
            </div>
            <div class="address-section">
                <h3>Pengirim</h3>
                <strong>DjudasMS Jakarta</strong><br>
                081234567890<br>
                Jl. Kebon Jeruk Raya No. 27<br>
                Jakarta Barat, DKI Jakarta<br>
                11530
            </div>
        </div>

        <div class="details">
            <h3>Detail Pesanan (Isi Paket)</h3>
            <table>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td style="text-align: right;">{{ $item->quantity }}x</td>
                </tr>
                @endforeach
            </table>
            <div style="margin-top: 15px; font-size: 12px; color: #666; border-top: 1px dashed #ccc; padding-top: 10px;">
                Catatan Penjual: Pastikan membongkar paket di depan kamera (video unboxing).
            </div>
        </div>
    </div>
</body>
</html>
