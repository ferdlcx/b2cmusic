<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Pembayaran Berhasil - Pesanan #' . $this->order->order_code)
                    ->greeting('Halo ' . $notifiable->name . '!')
                    ->line('Kami telah menerima pembayaran untuk pesanan Anda sebesar Rp ' . number_format($this->order->total, 0, ',', '.'))
                    ->line('Pesanan Anda saat ini sedang diproses oleh tim kami.')
                    ->action('Lihat Detail Pesanan', route('orders.show', $this->order->order_code))
                    ->line('Terima kasih telah berbelanja di MusicStore!');
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
