<?php

namespace App\Livewire;

use App\Models\Subtype;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Card;
use App\Models\Set;
use App\Models\Classe;
use App\Models\Edition;
use App\Models\Type;
use App\Models\Listing;
use App\Models\Wishlist;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Flux\Flux;


class CardFilter extends Component
{
    use WithPagination;

    public $sortBy = 'set_id';
    public $sortDirection = 'desc';
    public $viewType = 'list';

    public $search;
    public $edition = [];
    public $class = [];
    public $type = [];
    public $subtype = [];
    public $perPageOptions = [10, 20, 50, 100];
    public $perPage = 10;

    public $card_count = 1;
    public $price = 1;

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
    ];

    protected $rules = [
        'card_count' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0.01',
    ];

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

    public function getCardsProperty()
    {
        $query = Edition::query();

        if ($this->search) {
            $query->whereHas('card', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->edition)) {
            $query->whereIn('set_id', $this->edition);
        }

        if (!empty($this->class)) {
            $query->whereHas('card.classes', function ($q) {
                $q->whereIn('classes.id', $this->class);
            });
        }

        if (!empty($this->type)) {
            $query->whereHas('card.types', function ($q) {
                $q->whereIn('types.id', $this->type);
            });
        }

        if (!empty($this->subtype)) {
            $query->whereHas('card.subtypes', function ($q) {
                $q->whereIn('subtypes.id', $this->subtype);
            });
        }

        if ($this->sortBy === 'name') {
            $query->join('cards', 'editions.card_id', '=', 'cards.id')
                ->select('editions.*')
                ->addSelect('cards.name as card_name')
                ->orderBy('card_name', $this->sortDirection);
        } elseif ($this->sortBy === 'set_prefix') {
            $query->join('sets', 'editions.set_id', '=', 'sets.id')
                ->select('editions.*')
                ->addSelect('sets.prefix as set_prefix')
                ->orderBy('set_prefix', $this->sortDirection);
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $query->with(['card', 'set']);

        return $query->paginate($this->perPage);
    }


    public function render()
    {
        $sets = Set::all();
        $classes = Classe::all();
        $types = Type::all();
        $subtypes = Subtype::all();

        return view('livewire.card-filter', [
            'cards' => $this->cards,
            'sets' => $sets,
            'classes' => $classes,
            'types' => $types,
            'subtypes' => $subtypes
        ]);
    }

    public function addToWishlist($cardId)
    {
        if (auth()->check()) {
            $this->validate();

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
    }
    
    public function addToListing($cardId)
    {
        if (auth()->check()) {
            $this->validate();

            $listing = Listing::firstOrCreate(
                [
                    'user_id' => auth()->id(),
                    'edition_id' => $cardId,
                    'price' => $this->price,
                ],
                [
                    'card_count' => $this->card_count,
                ]
            );

            if ($listing->wasRecentlyCreated === false) {
                $listing->card_count += $this->card_count; 
                $listing->save();
            }

            Flux::toast('Card added to your listings!');
        } else {
            Flux::toast('You must be logged in to add to your listings.');
        }
        
        $this->modal('add-to-listing-' . $cardId)->close();
    }

}
