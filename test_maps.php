<?php
$ch = curl_init('https://api.biteship.com/v1/maps/areas?countries=ID&input=padamara&type=single');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: biteship_test.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoianVkYXMiLCJ1c2VySWQiOiI2YTI5NmFiNWU1NDcyNDcwZjA0MzI4NTUiLCJpYXQiOjE3ODExMDc5NDV9.CheR3TZkvTsBNyiB4sE8uvRkM277K6Cq-ocD_4o4pG8'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Code: $httpcode\n";
echo "Response: $response\n";
