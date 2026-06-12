<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apiKey = env('BITESHIP_API_KEY');
$order = \App\Models\Order::whereNotNull('biteship_order_id')->orderBy('id', 'desc')->first();
if (!$order) {
    echo "No order found";
    exit;
}

$orderId = $order->biteship_order_id;
echo "Testing order ID: $orderId\n";

$client = new \GuzzleHttp\Client();

try {
    $res = $client->request('POST', "https://api.biteship.com/v1/orders/{$orderId}", [
        'headers' => [
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'status' => 'allocated'
        ]
    ]);
    echo "Response Code: " . $res->getStatusCode() . "\n";
    echo $res->getBody() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse') && $e->getResponse()) {
        echo "Body: " . $e->getResponse()->getBody() . "\n";
    }
}
