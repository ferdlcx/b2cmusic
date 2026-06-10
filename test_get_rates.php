<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = new \Illuminate\Http\Request();
$req->replace(['destination_area_id' => 17465, 'weight' => 11500]);
$res = app()->make('App\Http\Controllers\RajaOngkirController')->getRates($req);
echo json_encode($res->getData());
