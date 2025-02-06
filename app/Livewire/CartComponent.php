<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Rinvex\Country\CountryLoader;
use Flux\Flux;

class CartComponent extends Component
{

    public function removeCartItem($cartItemUserId)
    {
        $userId = auth()->id();
        
        $cartItems = Cart::where('user_id', $userId)
                        ->whereHas('listing', function ($query) use ($cartItemUserId) {
                            $query->where('user_id', $cartItemUserId);
                        })
                        ->get();
    
        if ($cartItems->isNotEmpty()) {
            foreach ($cartItems as $cartItem) {
                $listing = $cartItem->listing;
    
                if ($listing) {
                    $listing->card_count += $cartItem->amount;
                    $listing->save();
                }
    
                $cartItem->delete();
            }
    
            Flux::toast(
                heading: 'Success',
                text: 'Cart items deleted.',
                variant: 'success',
            );
        } else {
            Flux::toast(
                heading: 'Error',
                text: 'No cart items found to delete.',
                variant: 'error',
            );
        }

        $this->modal('cart-item-' . $cartItemUserId . '-delete')->close();

    }

    public function buyCartItem($cartItemUserId)
    {
        $userId = auth()->id();

        // Retrieve the cart items for the logged-in user
        $cartItems = Cart::where('user_id', $userId)
                        ->whereHas('listing', function ($query) use ($cartItemUserId) {
                            $query->where('user_id', $cartItemUserId);
                        })
                        ->get();

        if ($cartItems->isNotEmpty()) {
            // Create the order
            $order = new Order([
                'user_id' => $cartItems->first()->user_id,
                'status' => 'pending',
            ]);
            $order->save();

            foreach ($cartItems as $cartItem) {
                $orderItem = new OrderItem([
                    'order_id' => $order->id, 
                    'listing_id' => $cartItem->listing->id,
                    'user_id' => $cartItem->user_id,
                    'amount' => $cartItem->amount,
                ]);

                $orderItem->save();
                $cartItem->delete();
            }

            Flux::toast(
                heading: 'Success',
                text: 'Order created successfully, status is pending.',
                variant: 'success',
            );
        } else {
            Flux::toast(
                heading: 'Error',
                text: 'No cart items found to purchase.',
                variant: 'error',
            );
        }
    }

    public function removeSingleCartItem($cartItemId)
    {
        $userId = auth()->id();
        
        $cartItem = Cart::where('id', $cartItemId)
                        ->where('user_id', $userId)
                        ->first();

        if ($cartItem) {
            $listing = $cartItem->listing;
            if ($listing) {
                $listing->card_count += $cartItem->amount;
                $listing->save();
            }

            $cartItem->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Item removed from cart.',
                variant: 'success',
            );
        } else {
            Flux::toast(
                heading: 'Error',
                text: 'Item not found.',
                variant: 'error',
            );
        }

        $this->modal('cart-item-' . $cartItemId . '-delete')->close();
    }

    public function render()
    {
        $userId = auth()->id();

        $groupedCart = Cart::with(['listing.user', 'user'])
            ->where('user_id', $userId)
            ->get()
            ->groupBy(fn($cartItem) => $cartItem->listing->user_id);

        $countries = collect(CountryLoader::countries())->mapWithKeys(function ($country) {
            return [
                $country['iso_3166_1_alpha2'] => [
                    'name' => $country['name'], // Country name
                    'flag' => $country['emoji'], // Country emoji
                ],
            ];
        })->toArray();

        return view('livewire.cart-component', [
            'groupedCart' => $groupedCart,
            'countries'=> $countries
        ]);
    }
}
