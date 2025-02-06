<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Rinvex\Country\CountryLoader;

class OrderDetails extends Component
{
    public $order;
    public $countries;

    public function mount($order)
    {
        $this->order = Order::with([
            'order_items.listing.user',
            'order_items.listing.edition.card',
            'user',
        ])->findOrFail((int) $order);

        $this->countries = collect(CountryLoader::countries())->mapWithKeys(function ($country) {
            return [
                $country['iso_3166_1_alpha2'] => [
                    'name' => $country['name'], // Country name
                    'flag' => $country['emoji'], // Country emoji
                ],
            ];
        })->toArray();
    }

    public function updateStatus($newStatus)
    {
        if ($newStatus === 'sent' && $this->order->order_items->first()->listing->user_id === auth()->id() && $this->order->status === 'pending') {
            $this->order->status = 'sent';
        } elseif ($newStatus === 'completed' && $this->order->user_id === auth()->id() && $this->order->status === 'sent') {
            $this->order->status = 'completed';
        } elseif ($newStatus === 'canceled' && $this->order->order_items->first()->listing->user_id === auth()->id() && in_array($this->order->status, ['pending', 'sent'])) {
            $this->order->status = 'canceled';

            // Return the quantity of items to the listing
            foreach ($this->order->order_items as $item) {
                $item->listing->increment('count', $item->amount);
            }
        } else {
            session()->flash('error', 'You are not authorized to change the status.');
            return;
        }

        $this->order->save();
        session()->flash('message', 'Order status updated to ' . ucfirst($newStatus) . '.');
    }

    public function render()
    {
        return view('livewire.order-details')->layout('layouts.app');    
    }
}

