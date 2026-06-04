<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCompleted extends Notification
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
            'title' => 'Pesanan Selesai',
            'body' => 'Pesanan #' . $this->order->order_code . ' telah selesai dan berhasil diterima oleh pelanggan. Terima kasih telah berbelanja!',
            'order_code' => $this->order->order_code,
            'order_id' => $this->order->id,
        ];
    }
}
