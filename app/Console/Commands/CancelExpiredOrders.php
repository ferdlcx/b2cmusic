<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan pesanan yang belum dibayar lebih dari 24 jam dan kembalikan stok';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredOrders = Order::with('items')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subHours(24))
            ->get();

        $count = 0;
        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                // Return stock
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }

                $order->update(['status' => 'canceled']);
                if ($order->payment) {
                    $order->payment->update(['status' => 'failed']);
                }

                try {
                    \App\Models\ActivityLog::create([
                        'user_id' => $order->user_id,
                        'action' => 'order_auto_canceled',
                        'model_type' => Order::class,
                        'model_id' => $order->id,
                        'description' => "Sistem membatalkan pesanan secara otomatis karena batas waktu pembayaran 24 jam habis.",
                        'ip_address' => '127.0.0.1',
                    ]);
                } catch (\Exception $e) {}
            });
            $count++;
        }

        Log::info("Auto-canceled {$count} expired orders.");
        $this->info("Successfully canceled {$count} expired orders.");
    }
}
