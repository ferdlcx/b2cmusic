<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Reset Password (OTP) DjudasMS</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            color: #334155;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #1c1917;
            padding: 40px 20px;
            text-align: center;
            border-bottom: 3px solid #eab308;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #0f172a;
        }
        .message {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #64748b;
        }
        .otp-box {
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 30px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: 8px;
            color: #eab308;
            margin: 0;
        }
        .warning {
            font-size: 13px;
            color: #ef4444;
            margin-top: 20px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DjudasMS</h1>
        </div>
        
        <div class="content">
            <div class="greeting">Halo, {{ $userName }}!</div>
            <div class="message">
                Kami menerima permintaan untuk mengatur ulang kata sandi akun DjudasMS Anda. Silakan gunakan kode reset password (OTP) di bawah ini untuk melanjutkan:
            </div>
            
            <div class="otp-box">
                <p class="otp-code">{{ $otpCode }}</p>
            </div>
            
            <div class="message" style="font-size: 14px;">
                Kode ini akan kedaluwarsa dalam <strong>5 hari</strong>. Untuk keamanan, jangan pernah membagikan kode ini kepada siapa pun, termasuk pihak DjudasMS.
            </div>
            
            <div class="warning">
                Jika Anda tidak meminta pengaturan ulang kata sandi ini, abaikan email ini dan pastikan akun Anda tetap aman.
            </div>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} DjudasMS Luxe. Hak cipta dilindungi undang-undang.<br>
            Email ini dikirim secara otomatis, mohon tidak membalas.
        </div>
    </div>
</body>
</html>
