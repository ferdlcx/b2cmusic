<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$addresses = \App\Models\Address::all()->toArray();
echo json_encode($addresses, JSON_PRETTY_PRINT) . PHP_EOL;
