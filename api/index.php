<?php

// Jalankan penyesuaian folder writable /tmp untuk serverless Vercel
$storageDirs = [
    '/tmp/storage/bootstrap/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Override path cache views agar ditulis ke /tmp (yang writable)
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Load index.php utama Laravel
require __DIR__ . '/../public/index.php';
