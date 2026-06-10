<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pesanan DjudasMS</title>
    <style>
        body { font-family: 'Inter', -apple-system, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; color: #334155; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #1c1917; padding: 40px 20px; text-align: center; border-bottom: 3px solid #eab308; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0f172a; text-align: center; }
        .message { font-size: 15px; line-height: 1.6; margin-bottom: 30px; color: #64748b; text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th { background-color: #f1f5f9; padding: 12px; text-align: left; font-size: 13px; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
        .table td { padding: 16px 12px; border-bottom: 1px solid #e2e8f0; font-size: 15px; color: #0f172a; }
        .total-row td { font-weight: 700; color: #1c1917; border-bottom: none; }
        .address-box { background-color: #f1f5f9; border-radius: 12px; padding: 20px; margin-bottom: 30px; font-size: 14px; line-height: 1.6; }
        .btn { display: inline-block; background-color: #1c1917; color: #eab308; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; text-transform: uppercase; tracking: widest; }
        .btn-container { text-align: center; margin-bottom: 20px; }
        .footer { background-color: #f8fafc; padding: 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>DjudasMS</h1></div>
        <div class="content">
            <div class="greeting">Halo, {{ $order->user->name ?? 'Pelanggan' }}!</div>
            <div class="message">
                Terima kasih atas pesanan Anda. Pembayaran untuk pesanan <strong>#{{ $order->order_code }}</strong> telah kami terima dan saat ini sedang kami proses untuk pengiriman.
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th style="text-align: right;">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @if($order->shipment)
                    <tr>
                        <td colspan="2" style="color: #64748b; font-size: 13px;">Ongkos Kirim ({{ $order->shipment->courier }})</td>
                        <td style="text-align: right;">Rp {{ number_format($order->shipment->shipping_cost, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right; padding-top: 20px;">Total Pembayaran:</td>
                        <td style="text-align: right; padding-top: 20px; color: #eab308; font-size: 18px;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="address-box">
                <strong>Alamat Pengiriman:</strong><br>
                {{ $order->address->name ?? '' }} ({{ $order->address->phone ?? '' }})<br>
                {{ $order->address->address ?? '' }}<br>
                {{ $order->address->city ?? '' }}, {{ $order->address->province ?? '' }} {{ $order->address->postal_code ?? '' }}
            </div>

            <div class="btn-container">
                <a href="{{ url('/orders/' . $order->order_code) }}" class="btn">Lacak Pesanan</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} DjudasMS Luxe. Hak cipta dilindungi undang-undang.<br>
            Email ini dikirim secara otomatis, mohon tidak membalas.
        </div>
    </div>
</body>
</html>
