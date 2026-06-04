<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_code }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        table td {
            padding: 6px;
            vertical-align: top;
        }
        .header-table td {
            padding: 0;
        }
        .logo {
            font-size: 20px;
            font-weight: 900;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .logo span {
            color: #4f46e5;
        }
        .invoice-title {
            text-align: right;
            font-size: 24px;
            font-weight: 300;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .meta-details {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .meta-details td {
            width: 50%;
            padding: 0 10px 0 0;
        }
        .details-heading {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 1px;
            margin-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }
        .items-table {
            margin-top: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 6px;
        }
        .items-table td {
            padding: 10px 6px;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-table {
            margin-top: 20px;
            width: 40%;
            float: right;
        }
        .total-table td {
            padding: 5px 6px;
        }
        .total-row td {
            font-weight: bold;
            font-size: 13px;
            color: #4f46e5;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        .footer {
            margin-top: 100px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="logo">DjudasMS <span>Luxe</span></div>
                    <p style="margin: 5px 0 0 0; color: #64748b; font-size: 10px;">
                        Model B2C E-commerce instrumen musik premium<br>
                        Jakarta, Indonesia
                    </p>
                </td>
                <td class="invoice-title">
                    Invoice
                </td>
            </tr>
        </table>

        <!-- Meta Details -->
        <table class="meta-details">
            <tr>
                <td>
                    <div class="details-heading">Informasi Tagihan</div>
                    <strong>{{ $order->user->name }}</strong><br>
                    Email: {{ $order->user->email }}<br>
                    Telp: {{ $order->address ? $order->address->phone : $order->user->phone }}
                </td>
                <td>
                    <div class="details-heading">Detail Invoice</div>
                    No. Invoice: <strong>#{{ $order->order_code }}</strong><br>
                    Tanggal: {{ $order->created_at->format('d F Y, H:i') }} WIB<br>
                    Metode Pembayaran: {{ $order->payment ? strtoupper(str_replace('_', ' ', $order->payment->payment_type)) : '-' }}<br>
                    Status: <strong>{{ strtoupper($order->status) }}</strong>
                </td>
            </tr>
            @if($order->address)
                <tr>
                    <td colspan="2" style="padding-top: 15px;">
                        <div class="details-heading">Alamat Pengiriman</div>
                        <strong>{{ $order->address->name }}</strong> ({{ $order->address->label }})<br>
                        {{ $order->address->address }}, {{ $order->address->village }}, {{ $order->address->district }}<br>
                        {{ $order->address->city }}, {{ $order->address->province }}, {{ $order->address->postal_code }}
                    </td>
                </tr>
            @endif
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 50%;">Produk</th>
                    <th style="width: 15%; text-align: right;">Harga Satuan</th>
                    <th style="width: 10%; text-align: center;">Jumlah</th>
                    <th style="width: 20%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product ? $item->product->name : 'Produk Tidak Ditemukan' }}</strong>
                            @if($item->product && $item->product->sku)
                                <br><span style="font-size: 8px; color: #94a3b8; font-family: monospace;">SKU: {{ $item->product->sku }}</span>
                            @endif
                        </td>
                        <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Table -->
        <table class="total-table">
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Kirim:</td>
                <td style="text-align: right;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
            </tr>
            @if($order->discount > 0)
                <tr style="color: #e11d48;">
                    <td>Diskon:</td>
                    <td style="text-align: right;">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total Tagihan:</td>
                <td style="text-align: right;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <div style="clear: both;"></div>

        <!-- Footer -->
        <div class="footer">
            Terima kasih telah berbelanja di DjudasMS.<br>
            Jika Anda memiliki pertanyaan mengenai invoice ini, silakan hubungi Customer Support kami.
        </div>
    </div>
</body>
</html>
