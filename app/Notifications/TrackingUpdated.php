<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrackingUpdated extends Notification
{
    use Queueable;

    protected $order;
    protected $statusLabel;
    protected $source;

    public function __construct(Order $order, $statusLabel, $source = 'system')
    {
        $this->order = $order;
        $this->statusLabel = $statusLabel;
        $this->source = $source;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $trackingNumber = $this->order->shipment ? $this->order->shipment->tracking_number : null;
        $body = 'Status pengiriman pesanan #' . $this->order->order_code . ' telah diupdate menjadi: ' . $this->statusLabel . '.';
        
        if ($trackingNumber) {
            $body .= ' (Resi: ' . $trackingNumber . ')';
        }

        return [
            'title' => 'Update Pengiriman Pesanan',
            'body' => $body,
            'order_code' => $this->order->order_code,
            'order_id' => $this->order->id,
            'source' => $this->source
        ];
    }
}
