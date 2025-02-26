<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Notifications\OrderStatusChanged;

class OrderComponent extends Component
{
    public function updateStatus($newStatus, $orderId)
    {
        $order = Order::with('order_items.listing')->find($orderId);

        if (!$order) {
            session()->flash('error', 'Order not found.');
            return;
        }

        $seller = $order->order_items->first()->listing->user;
        $buyer = $order->user;
        $authUser = auth()->user();

        if (
            $newStatus === 'sent' &&
            $seller->id === $authUser->id &&
            $order->status === 'pending'
        ) {
            $order->status = 'sent';
            $buyer->notify(new OrderStatusChanged($order, 'sent')); // Notify Buyer
        } elseif (
            $newStatus === 'completed' &&
            $buyer->id === $authUser->id &&
            $order->status === 'sent'
        ) {
            $order->status = 'completed';
            $seller->notify(new OrderStatusChanged($order, 'completed')); // Notify Seller
        } elseif (
            $newStatus === 'canceled' &&
            $seller->id === $authUser->id &&
            in_array($order->status, ['pending', 'sent'])
        ) {
            $order->status = 'canceled';
            $buyer->notify(new OrderStatusChanged($order, 'canceled')); // Notify Buyer

            // Return quantity to listing
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
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId) // Orders where the user is the buyer
                    ->orWhereHas('order_items.listing', function ($q) use ($userId) {
                        $q->where('user_id', $userId); // Orders where the user is the seller
                    });
            })
            ->get()
            ->groupBy(function ($order) {
                return $order->id; // Group orders by their IDs for easier organization
            });

        return view('livewire.order-component', [
            'orders' => $orders,
        ]);
    }

}
