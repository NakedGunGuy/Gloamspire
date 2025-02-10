<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Wishlist;
use App\Models\Set;
use App\Models\Classe;
use App\Models\Type;
use App\Models\Edition;
use App\Models\Subtype;
use App\Models\Card;
use App\Models\OrderItem;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Rinvex\Country\CountryLoader;

class WishlistComponent extends Component
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

    public $amount = 1;

    protected $queryString = ['search', 'edition', 'class', 'type', 'subtype', 'perPage', 'viewType'];

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getWishlistsProperty()
    {
        $query = Wishlist::query()->where('card_count', '>', 0)
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

        if ($this->sortBy === 'set_prefix') {
            $query->join('editions', 'wishlists.edition_id', '=', 'editions.id')
                ->join('sets', 'editions.set_id', '=', 'sets.id')
                ->select('wishlists.*', 'sets.prefix as set_prefix')
                ->orderBy('set_prefix', $this->sortDirection);
        } elseif ($this->sortBy === 'name') {
            $query->join('editions', 'wishlists.edition_id', '=', 'editions.id')
                ->join('cards', 'editions.card_id', '=', 'cards.id')
                ->select('wishlists.*', 'cards.name as card_name')
                ->orderBy('card_name', $this->sortDirection);
        } elseif ($this->sortBy === 'collector_number') {
            $query->join('editions', 'wishlists.edition_id', '=', 'editions.id')
                ->select('wishlists.*', 'editions.collector_number as edition_number')
                ->orderBy('edition_number', $this->sortDirection);
        } elseif ($this->sortBy === 'rarity') {
            $query->join('editions', 'wishlists.edition_id', '=', 'editions.id')
                ->select('wishlists.*', 'editions.rarity as rarity')
                ->orderBy('rarity', $this->sortDirection);
        } elseif ($this->sortBy === 'user') {
            $query->join('users', 'wishlists.user_id', '=', 'users.id')
                ->select('wishlists.*', 'users.name as name')
                ->orderBy('name', $this->sortDirection);
        } elseif ($this->sortBy === 'country') {
            $query->join('users', 'wishlists.user_id', '=', 'users.id')
                ->select('wishlists.*', 'users.country as country')
                ->orderBy('country', $this->sortDirection);
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
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
        
        return view('livewire.wishlist-component', [
            'wishlists' => $this->wishlists,
            'sets' => $sets,
            'classes' => $classes,
            'types' => $types,
            'subtypes' => $subtypes,
            'countries'=> $countries
        ]);
    }

    public function removeWishlist($wishlistId)
    {
        $wishlist = Wishlist::findOrFail($wishlistId);
        
        if ($wishlist->user_id === auth()->id()) {
            $wishlist->delete();
            Flux::toast('Item removed from wishlist.');
        }
    }


}
