<?php
require __DIR__.'/vendor/autoload.php';

$apiKey = 'biteship_test.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoianVkYXMiLCJ1c2VySWQiOiI2YTI5NmFiNWU1NDcyNDcwZjA0MzI4NTUiLCJpYXQiOjE3ODExNjM5NzV9.YbrI_t3PHWqGcpvOe_I1yjJ9OV306R8xv0OatnO9vN8';

$client = new \GuzzleHttp\Client();

try {
    // Create an order first
    $createRes = $client->request('POST', 'https://api.biteship.com/v1/orders', [
        'headers' => [
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json'
        ],
        'json' => [
            "shipper_contact_name" => "Amir",
            "shipper_contact_phone" => "088888888888",
            "origin_contact_name" => "Amir",
            "origin_contact_phone" => "088888888888",
            "origin_address" => "Plaza Senayan, Jalan Asia Afrik...",
            "origin_postal_code" => 12440,
            "destination_contact_name" => "John Doe",
            "destination_contact_phone" => "088888888888",
            "destination_address" => "Lebak Bulus MRT...",
            "destination_postal_code" => 12950,
            "courier_company" => "jne",
            "courier_type" => "reg",
            "delivery_type" => "now",
            "items" => [
                [
                    "name" => "Black L",
                    "value" => 165000,
                    "quantity" => 1,
                    "weight" => 200
                ]
            ]
        ]
    ]);
    
    $orderData = json_decode($createRes->getBody(), true);
    $orderId = $orderData['id'];
    echo "Created Order ID: " . $orderId . "\n";
    
    // Now try to advance status via POST /v1/orders/:id/status
    echo "Advancing to allocated...\n";
    $updateRes = $client->request('POST', "https://api.biteship.com/v1/orders/{$orderId}/status", [
        'headers' => [
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'status' => 'allocated'
        ]
    ]);
    
    echo "Update Response Code: " . $updateRes->getStatusCode() . "\n";
    echo $updateRes->getBody() . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse') && $e->getResponse()) {
        echo "Body: " . $e->getResponse()->getBody() . "\n";
    }
}
