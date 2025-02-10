<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Card;
use App\Models\Edition;
use App\Models\Listing;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Cart;
use Flux\Flux;
use App\Models\Wishlist;
use Livewire\WithPagination;

class CardDetails extends Component
{
    public $cardId;
    public $card;
    public $editions;
    public $salesData;
    public $selectedEditionId;
    public $editionPrefix;
    public $price;  // Add any other form input variables here
    public $card_count = 1;  // Default card count
    public $amount;
    
    use WithPagination;

    public function mount($cardId)
    {
        $this->cardId = $cardId;
        $this->editionPrefix = request()->query('edition', null);
        $this->loadCardData();
    }

    public function addToWishlist($cardId)
    {
        if (auth()->check()) {
            $this->validate(['card_count' => 'required|integer|min:1',]);  // Use the validation

            $wishlist = Wishlist::firstOrCreate(
                [
                    'user_id' => auth()->id(),
                    'edition_id' => $cardId,
                ],
                [
                    'card_count' => $this->card_count,
                ]
            );

            if ($wishlist->wasRecentlyCreated === false) {
                $wishlist->card_count += $this->card_count; 
                $wishlist->save();
            }

            Flux::toast('Card added to your wishlist!');
        } else {
            Flux::toast('You must be logged in to add to your wishlist.');
        }
        
        $this->modal('add-to-wishlist-' . $cardId)->close();

        $this->loadCardData();

    }

    public function addToListing($cardId)
    {   
        if (auth()->check()) {
            $this->validate([
                'price' => 'required|numeric|min:0',
                'card_count' => 'required|integer|min:1',
            ]);

            $newlisting = Listing::firstOrCreate(
                [
                    'user_id' => auth()->id(),
                    'edition_id' => $cardId,
                    'price' => $this->price,
                ],
                [
                    'card_count' => $this->card_count,
                ]
            );

            if ($newlisting->wasRecentlyCreated === false) {
                $newlisting->card_count += $this->card_count; 
                $newlisting->save();
            }

            Flux::toast('Card added to your listings!');
        } else {
            Flux::toast('You must be logged in to add to your listings.');
        }

        $this->modal('add-to-listing-' . $cardId)->close();

        $this->loadCardData();
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
                text: 'Listing is part of active or pending orders, amount set to 0'
            );
        }

        $this->modal('add-to-listing-' . $listingId . '-delete')->close();

        $this->loadCardData();
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

        $this->loadCardData();
    }

    public function loadCardData()
    {
        $this->card = Card::with(['editions.set'])->findOrFail($this->cardId);
        
        $this->editions = $this->card->editions;
    
        if ($this->editionPrefix) {
            $selectedEdition = $this->editions->firstWhere('set.prefix', $this->editionPrefix);
        } else {
            $selectedEdition = $this->editions->first();
        }
    
        $this->selectedEditionId = $selectedEdition?->id;
        $this->card->currentEdition = $selectedEdition;
    
        $this->salesData = OrderItem::where('edition_id', $this->selectedEditionId)
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->join('listings', 'order_items.listing_id', '=', 'listings.id')
            ->select('listings.price', 'order_items.created_at')
            ->orderBy('order_items.created_at', 'desc')
            ->get();
    }    

    public function render()
    {
        return view('livewire.card-details', [
            'listings' => Listing::where('edition_id', $this->selectedEditionId)
                ->where('card_count', '>', 0)
                ->with('edition', 'edition.set')
                ->orderBy('updated_at', 'desc')
                ->paginate(10),
        ])->layout('layouts.app');
    }
}
