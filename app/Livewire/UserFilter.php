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
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Rinvex\Country\CountryLoader;

class UserFilter extends Component
{
    use WithPagination;

    public $sortBy = 'name';
    public $sortDirection = 'desc';

    public $search;
    public $perPageOptions = [10, 20, 50, 100];
    public $perPage = 10;

    protected $queryString = ['search', 'perPage'];

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getListingsProperty()
    {
        $query = User::query();
        //->where('user_id', !auth()->id());

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
    
        $query->orderBy($this->sortBy, $this->sortDirection);
        
        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $countries = collect(CountryLoader::countries())->mapWithKeys(function ($country) {
            return [
                $country['iso_3166_1_alpha2'] => [
                    'name' => $country['name'], // Country name
                    'flag' => $country['emoji'], // Country emoji
                ],
            ];
        })->toArray();
        
        return view('livewire.user-filter', [
            'users' => $this->listings,
            'countries'=> $countries
        ]);
    }
}
