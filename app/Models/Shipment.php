<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Shipment extends Model
{
    protected $fillable = [
        'order_id', 'courier', 'service', 'tracking_number', 
        'biteship_order_id', 'biteship_waybill_id', 'shipping_cost', 
        'status', 'status_history', 'shipped_at', 'delivered_at'
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'status_history' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function appendStatus($status, $description, $location, $source = 'system', $lat = null, $lng = null)
    {
        $history = $this->status_history ?? [];
        $history[] = [
            'status' => $status,
            'description' => $description,
            'location' => $location,
            'lat' => $lat,
            'lng' => $lng,
            'datetime' => now()->format('Y-m-d H:i:s'),
            'source' => $source
        ];

        $this->update([
            'status' => $status,
            'status_history' => $history
        ]);
        
        // Notify user about tracking update
        try {
            if ($this->order && $this->order->user) {
                // Don't spam notifications for 'confirmed' if it's the first one just created at checkout
                if ($status !== 'confirmed' || count($history) > 1) {
                    $this->order->user->notify(new \App\Notifications\TrackingUpdated($this->order, $status, $source));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send TrackingUpdated notification: ' . $e->getMessage());
        }
    }
}
