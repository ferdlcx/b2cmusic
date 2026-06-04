<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

// Override config dynamically
Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
Config::set('mail.mailers.smtp.port', 587);
Config::set('mail.mailers.smtp.encryption', 'tls');
Config::set('mail.mailers.smtp.username', 'gakdi940@gmail.com');
Config::set('mail.mailers.smtp.password', 'ytjxkvlwyrrjwtpv');
Config::set('mail.from.address', 'gakdi940@gmail.com');
Config::set('mail.from.name', 'DjudasMS Testing');

try {
    Mail::raw('Ini adalah email testting yang dikirim secara manual. Konfigurasi App Password Anda berfungsi dengan baik!', function ($message) {
        $message->to('gakdi940@gmail.com')
                ->subject('testting - Manual dari CLI');
    });
    echo "BERHASIL: Email berhasil dikirim ke gakdi940@gmail.com!\n";
} catch (\Exception $e) {
    echo "GAGAL: " . $e->getMessage() . "\n";
}
