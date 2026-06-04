<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCreated extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Pesanan Berhasil Dibuat',
            'body' => 'Pesanan #' . $this->order->order_code . ' telah berhasil dibuat dengan total Rp ' . number_format($this->order->total, 0, ',', '.') . '. Silakan lakukan simulasi pembayaran.',
            'order_code' => $this->order->order_code,
            'order_id' => $this->order->id,
        ];
    }
}
