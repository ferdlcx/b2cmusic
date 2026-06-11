<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.biteship.com/v1/rates/couriers",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
      "origin_area_id" => "IDNP6IDNC147IDND829",
      "destination_area_id" => "IDNP6IDNC147IDND832",
      "couriers" => "jne,sicepat,jnt",
      "items" => [
          [
              "name" => "Guitar",
              "description" => "Acoustic",
              "value" => 1500000,
              "length" => 100,
              "width" => 40,
              "height" => 15,
              "weight" => 2000,
              "quantity" => 1
          ]
      ]
  ]),
  CURLOPT_HTTPHEADER => array(
    "authorization: biteship_test.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoianVkYXMiLCJ1c2VySWQiOiI2YTI5NmFiNWU1NDcyNDcwZjA0MzI4NTUiLCJpYXQiOjE3ODExMDc5NDV9.CheR3TZkvTsBNyiB4sE8uvRkM277K6Cq-ocD_4o4pG8",
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
