<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = App\Models\Order::whereNotNull('biteship_order_id')->get();
echo "Total orders with biteship: " . count($orders) . "\n";
foreach ($orders as $o) {
    echo "Order {$o->order_code}: biteship_order_id={$o->biteship_order_id}, tracking_id={$o->tracking_id}, waybill_id={$o->waybill_id}\n";
}
