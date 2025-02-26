<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $newStatus;

    public function __construct($order, $newStatus)
    {
        $this->order = $order;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Store in DB + real-time broadcast
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'new_status' => $this->newStatus,
            'message' => "Order #{$this->order->id} status changed to {$this->newStatus}."
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'new_status' => $this->newStatus,
            'message' => "Order #{$this->order->id} status changed to {$this->newStatus}."
        ]);
    }
}
