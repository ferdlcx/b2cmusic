<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderShipped extends Notification
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
        $trackingNumber = $this->order->shipment ? $this->order->shipment->tracking_number : null;
        $body = 'Pesanan #' . $this->order->order_code . ' telah diserahkan ke kurir dan sedang dikirim.';
        if ($trackingNumber) {
            $body .= ' Nomor resi: ' . $trackingNumber;
        }

        return [
            'title' => 'Pesanan Sedang Dikirim',
            'body' => $body,
            'order_code' => $this->order->order_code,
            'order_id' => $this->order->id,
        ];
    }
}
