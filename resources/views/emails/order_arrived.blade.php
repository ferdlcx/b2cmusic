<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Tiba DjudasMS</title>
    <style>
        body { font-family: 'Inter', -apple-system, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; color: #334155; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #1c1917; padding: 40px 20px; text-align: center; border-bottom: 3px solid #eab308; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; }
        .content { padding: 40px 30px; text-align: center; }
        .greeting { font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0f172a; }
        .message { font-size: 15px; line-height: 1.6; margin-bottom: 30px; color: #64748b; }
        .icon { font-size: 48px; margin-bottom: 20px; }
        .btn { display: inline-block; background-color: #1c1917; color: #eab308; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; text-transform: uppercase; tracking: widest; }
        .btn-container { margin-top: 30px; margin-bottom: 20px; }
        .warning { font-size: 13px; color: #eab308; margin-top: 20px; background-color: #1c1917; padding: 15px; border-radius: 8px; text-align: left; }
        .footer { background-color: #f8fafc; padding: 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>DjudasMS</h1></div>
        <div class="content">
            <div class="icon">📦</div>
            <div class="greeting">Halo, {{ $order->user->name ?? 'Pelanggan' }}!</div>
            <div class="message">
                Kabar gembira! Pesanan Anda dengan nomor resi <strong>{{ $order->shipment->tracking_number ?? '-' }}</strong> telah tiba di alamat tujuan.
            </div>
            
            <div class="warning">
                <strong>PENTING:</strong><br>
                Mohon periksa kelengkapan dan kondisi barang Anda. Jika semuanya sudah sesuai, mohon segera klik tombol "Konfirmasi Pesanan Diterima" di website kami agar dana dapat diteruskan ke penjual.
            </div>

            <div class="btn-container">
                <a href="{{ url('/orders/' . $order->order_code) }}" class="btn">Konfirmasi Pesanan</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} DjudasMS Luxe. Hak cipta dilindungi undang-undang.<br>
            Email ini dikirim secara otomatis, mohon tidak membalas.
        </div>
    </div>
</body>
</html>
