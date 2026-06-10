<?php
$key = '1oKzkr5Qf967fe03de1d601bxUErSPD8';
$url = 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=mengwi';
$options = [
    'http' => [
        'header' => "key: $key\r\n"
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "Error: " . $http_response_header[0] . "\n";
} else {
    echo $response;
}
