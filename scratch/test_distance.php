<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\RajaOngkirController;
$controller = new RajaOngkirController();

$ref = new ReflectionClass(RajaOngkirController::class);
$calculateDistance = $ref->getMethod('calculateDistance');
$calculateDistance->setAccessible(true);
$getApproximateCoordinates = $ref->getMethod('getApproximateCoordinates');
$getApproximateCoordinates->setAccessible(true);

// Test distance from Kebayoran Lama (-6.2253, 106.7994) to Bandung (-6.9175, 107.6191)
$dist = $calculateDistance->invokeArgs($controller, [-6.2253, 106.7994, -6.9175, 107.6191]);
echo "Distance to Bandung: {$dist} km (Expected ~110-120 km)" . PHP_EOL;

// Test approximation
$coords = $getApproximateCoordinates->invokeArgs($controller, ['Surabaya', 'Jawa Timur']);
echo "Approximate coords for Surabaya: " . json_encode($coords) . PHP_EOL;
