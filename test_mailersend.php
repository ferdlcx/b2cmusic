<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\Mail::raw('Test MailerSend SMTP', function ($msg) { 
        $msg->to('gakdi940@gmail.com')->subject('Testing MailerSend'); 
    });
    echo "SUCCESS: Email sent.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
