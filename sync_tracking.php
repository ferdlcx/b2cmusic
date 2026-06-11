<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = \App\Models\Order::whereNotNull('biteship_order_id')->whereNull('tracking_id')->get();
foreach ($orders as $order) {
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => env('BITESHIP_API_KEY')
    ])->get('https://api.biteship.com/v1/orders/' . $order->biteship_order_id);
    
    if ($response->successful()) {
        $data = $response->json();
        $tracking_id = $data['courier']['tracking_id'] ?? null;
        $waybill_id = $data['courier']['waybill_id'] ?? null;
        
        $order->update([
            'tracking_id' => $tracking_id,
            'waybill_id' => $waybill_id
        ]);
        echo "Updated order {$order->id} with tracking_id {$tracking_id}\n";
    }
}
