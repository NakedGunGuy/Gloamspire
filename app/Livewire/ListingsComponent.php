<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Listing;
use App\Models\Set;
use App\Models\Classe;
use App\Models\Type;
use App\Models\Edition;
use App\Models\Subtype;
use App\Models\Card;
use App\Models\Cart;
use App\Models\OrderItem;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Rinvex\Country\CountryLoader;

class ListingsComponent extends Component
{
    use WithPagination;

    public $userId;
    public $sortBy = 'edition_id';
    public $sortDirection = 'desc';
    public $viewType = 'list';

    public $search;
    public $edition = [];
    public $class = [];
    public $type = [];
    public $subtype = [];
    public $perPageOptions = [10, 20, 50, 100];
    public $perPage = 10;
    public $isFoil = null;

    public $amount = 1;

    protected $queryString = [
        'search', 
        'edition', 
        'class', 
        'type', 
        'subtype', 
        'perPage', 
        'viewType',
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'isFoil'
    ];

    public function updatedIsFoil()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedEdition()
    {
        $this->resetPage();
    }

    public function updatedClass()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function updatedSubtype()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();

    }

    public function getListingsProperty()
    {
        $query = Listing::query()->where('card_count', '>', 0)
        ->where('user_id', $this->userId ?? auth()->id());

        if ($this->search) {
            $query->whereHas('edition.card', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }
    
        if (!empty($this->edition)) {
            $query->whereHas('edition.set', function ($q) {
                $q->whereIn('sets.id', $this->edition);
            });
        }
    
        if (!empty($this->class)) {
            $query->whereHas('edition.card.classes', function ($q) {
                $q->whereIn('classes.id', $this->class);
            });
        }
    
        if (!empty($this->type)) {
            $query->whereHas('edition.card.types', function ($q) {
                $q->whereIn('types.id', $this->type);
            });
        }
    
        if (!empty($this->subtype)) {
            $query->whereHas('edition.card.subtypes', function ($q) {
                $q->whereIn('subtypes.id', $this->subtype);
            });
        }

        if ($this->isFoil !== null) {
            $query->where('is_foil', $this->isFoil);
        }

        if ($this->sortBy === 'set_prefix') {
            $query->join('editions', 'listings.edition_id', '=', 'editions.id')
                ->join('sets', 'editions.set_id', '=', 'sets.id')
                ->select('listings.*', 'sets.prefix as set_prefix')
                ->orderBy('set_prefix', $this->sortDirection);
        } elseif ($this->sortBy === 'name') {
            $query->join('editions', 'listings.edition_id', '=', 'editions.id')
                ->join('cards', 'editions.card_id', '=', 'cards.id')
                ->select('listings.*', 'cards.name as card_name')
                ->orderBy('card_name', $this->sortDirection);
        } elseif ($this->sortBy === 'collector_number') {
            $query->join('editions', 'listings.edition_id', '=', 'editions.id')
                ->select('listings.*', 'editions.collector_number as edition_number')
                ->orderBy('edition_number', $this->sortDirection);
        } elseif ($this->sortBy === 'rarity') {
            $query->join('editions', 'listings.edition_id', '=', 'editions.id')
                ->select('listings.*', 'editions.rarity as rarity')
                ->orderBy('rarity', $this->sortDirection);
        } elseif ($this->sortBy === 'user') {
            $query->join('users', 'listings.user_id', '=', 'users.id')
                ->select('listings.*', 'users.name as name')
                ->orderBy('name', $this->sortDirection);
        } elseif ($this->sortBy === 'country') {
            $query->join('users', 'listings.user_id', '=', 'users.id')
                ->select('listings.*', 'users.country as country')
                ->orderBy('country', $this->sortDirection);
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $query->withSum('carts as cart_count_sum', 'amount');

        return $query->paginate($this->perPage);
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
        $sets = Set::all();
        $classes = Classe::all();
        $types = Type::all();
        $subtypes = Subtype::all();
        $countries = collect(CountryLoader::countries())->mapWithKeys(function ($country) {
            return [
                $country['iso_3166_1_alpha2'] => [
                    'name' => $country['name'], // Country name
                    'flag' => $country['emoji'], // Country emoji
                ],
            ];
        })->toArray();
        
        return view('livewire.listings-component', [
            'listings' => $this->listings,
            'sets' => $sets,
            'classes' => $classes,
            'types' => $types,
            'subtypes' => $subtypes,
            'countries'=> $countries
        ]);
    }
}
