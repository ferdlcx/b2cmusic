<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentSuccess extends Notification
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
            'title' => 'Pembayaran Berhasil',
            'body' => 'Pembayaran untuk pesanan #' . $this->order->order_code . ' sebesar Rp ' . number_format($this->order->total, 0, ',', '.') . ' telah kami terima. Pesanan Anda sedang diproses.',
            'order_code' => $this->order->order_code,
            'order_id' => $this->order->id,
        ];
    }
}
