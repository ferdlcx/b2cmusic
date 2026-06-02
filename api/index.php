<?php

// Enable error reporting for debugging Vercel deployment issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure writable storage directories exist in the Vercel lambda container (/tmp)
$storageDirs = [
    '/tmp/storage/bootstrap/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Override path cache views so they write to /tmp
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Load index.php utama Laravel
require __DIR__ . '/../public/index.php';

