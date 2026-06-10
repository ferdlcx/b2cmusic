<?php
$key = '1oKzkr5Qf967fe03de1d601bxUErSPD8';
$url = 'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost';
$data = json_encode([
    'origin' => 17464,
    'destination' => 26693, // Mengwi
    'weight' => 1000,
    'courier' => 'jne'
]);

$options = [
    'http' => [
        'method' => 'POST',
        'header' => "key: $key\r\nContent-Type: application/json\r\n",
        'content' => $data
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "Error: " . $http_response_header[0] . "\n";
} else {
    echo $response;
}
