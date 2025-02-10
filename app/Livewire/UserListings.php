<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Listing;
use App\Models\Cart;
use App\Models\OrderItem;
use Flux\Flux;

class UserListings extends Component
{
    public $userId; // The user ID passed to the component
    public $amount = 1;

    public function getUserProperty()
    {
        return User::find($this->userId);
    }

    public function getListingsProperty()
    {
        $user = $this->getUserProperty();
        return $user ? $user->listings()->where('card_count', '>', 0)->get() : [];
    }

    public function removeListing($listingId)
    {
        $listing = Listing::findOrFail($listingId);
    
        if ($listing->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $cartItems = Cart::where('listing_id', $listingId)->where('user_id', auth()->id())->get();

        $orderItems = OrderItem::where('listing_id', $listingId)
        ->whereHas('order', function ($query) {
            $query->whereNotIn('status', ['completed', 'canceled']);
        })
        ->get();
        
        $listing->card_count = 0;
        $listing->save();
    
        if ($cartItems->isNotEmpty()) {
            Flux::toast('Listing found in cart, amount set to 0');
        } elseif ($orderItems->isNotEmpty()) {
            Flux::toast(
                variant: 'danger',
                text: 'Cannot delete listing. It is part of active or pending orders.'
            );
        }

        $this->modal('add-to-listing-' . $listingId . '-delete')->close();
    }

    public function addToCart($listingId)
    {
        if (auth()->check()) {
            $this->validate([
                'amount' => 'required|integer|min:1',
            ]);

            $listing = Listing::findOrFail($listingId);

            if ($listing->card_count < $this->amount) {
                Flux::toast(
                    variant: 'danger',
                    text: 'Not enough cards available in the listing.',
                );
                return;
            }

            $cart = Cart::firstOrCreate(
                [
                    'user_id' => auth()->id(),
                    'listing_id' => $listingId,
                ],
                [
                    'amount' => $this->amount,
                ]
            );

            if (!$cart->wasRecentlyCreated) {
                $cart->amount += $this->amount;
                $cart->save();
            }

            $listing->card_count -= $this->amount;
            $listing->save();

            Flux::toast('Card added to your cart!');
        } else {
            Flux::toast('You must be logged in to add to your cart.');
        }

        $this->modal('add-to-cart-' . $listingId)->close();
    }

    public function render()
    {
        return view('livewire.user-listings', [
            'userListings' => $this->getListingsProperty(),
        ]);
    }
}
