<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Rinvex\Country\CountryLoader;
use Flux\Flux;

class CountryFlag extends Component
{
    public string $countryCode = 'sl';

    /**
     * Create a new component instance.
     */
    public function __construct(string $countryCode)
    {
        $this->countryCode = strtolower($countryCode);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.country-flag', [
            'country' => CountryLoader::countries()[$this->countryCode] ?? null,
        ]);
    }
}
