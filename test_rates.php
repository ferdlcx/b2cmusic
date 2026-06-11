<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$payload = [
    'origin_postal_code' => 12440,
    'destination_postal_code' => 12950,
    'couriers' => 'jne,sicepat,jnt,pos,anteraja,tiki,grab,gojek',
    'items' => [
        [
            'name' => 'Produk DjudasMS',
            'description' => 'Pembelian dari DjudasMS',
            'value' => 100000,
            'length' => 10,
            'width' => 10,
            'height' => 10,
            'weight' => 1000,
            'quantity' => 1
        ]
    ]
];

$response = Illuminate\Support\Facades\Http::timeout(10)->withHeaders([
    'Authorization' => env('BITESHIP_API_KEY'),
    'Content-Type' => 'application/json'
])->post('https://api.biteship.com/v1/rates/couriers', $payload);

echo $response->body();
