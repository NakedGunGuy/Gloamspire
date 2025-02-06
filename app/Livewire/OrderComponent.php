<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;

class OrderComponent extends Component
{
    public function updateStatus($newStatus, $orderId)
    {
        $order = Order::with('order_items.listing')->find($orderId);

        if (!$order) {
            session()->flash('error', 'Order not found.');
            return;
        }

        if (
            $newStatus === 'sent' &&
            $order->order_items->first()->listing->user_id === auth()->id() &&
            $order->status === 'pending'
        ) {
            $order->status = 'sent';
        } elseif (
            $newStatus === 'completed' &&
            $order->user_id === auth()->id() &&
            $order->status === 'sent'
        ) {
            $order->status = 'completed';
        } elseif (
            $newStatus === 'canceled' &&
            $order->order_items->first()->listing->user_id === auth()->id() &&
            in_array($order->status, ['pending', 'sent'])
        ) {
            $order->status = 'canceled';

            // Return the quantity of items to the listing
            foreach ($order->order_items as $item) {
                $item->listing->increment('count', $item->amount);
            }
        } else {
            session()->flash('error', 'You are not authorized to change the status.');
            return;
        }

        $order->save();
        session()->flash('message', 'Order status updated to ' . ucfirst($newStatus) . '.');
    }

    public function render()
    {
        $userId = auth()->id();

        $orders = Order::with([
                'order_items.listing.user', // Load the listing and user associated with the order item
                'order_items.listing.edition.card', // Load the card details through edition
                'user' // Load the user who owns the order
            ])
            ->where('user_id', $userId)
            ->get()
            ->groupBy(function ($order) {
                return $order->id; // Group orders by their IDs for easier organization
            });

        return view('livewire.order-component', [
            'orders' => $orders,
        ]);
    }

}
