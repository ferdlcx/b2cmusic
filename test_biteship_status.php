<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['BITESHIP_API_KEY'];

// Let's create a draft order first or use an existing one to test.
// First get the latest order id from local DB
$pdo = new PDO("sqlite:" . __DIR__ . "/database/database.sqlite");
$stmt = $pdo->query("SELECT biteship_order_id FROM orders WHERE biteship_order_id IS NOT NULL ORDER BY id DESC LIMIT 1");
$row = $stmt->fetch();
$orderId = $row['biteship_order_id'];

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
    if ($e->hasResponse()) {
        echo $e->getResponse()->getBody() . "\n";
    }
}
