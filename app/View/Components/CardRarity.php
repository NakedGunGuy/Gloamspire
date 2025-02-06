<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CardRarity extends Component
{

    public $rarity;
    public $color;
    public $label;
    public $acronym;

    protected $rarityColors = [
        1 => ['color' => 'gray', 'label' => 'Common', 'acronym' => 'C'],
        2 => ['color' => 'green', 'label' => 'Uncommon', 'acronym' => 'U'],
        3 => ['color' => 'blue', 'label' => 'Rare', 'acronym' => 'R'],
        4 => ['color' => 'purple', 'label' => 'Super Rare', 'acronym' => 'SR'],
        5 => ['color' => 'yellow', 'label' => 'Ultra Rare', 'acronym' => 'UR'],
        6 => ['color' => 'teal', 'label' => 'Promotional Rate', 'acronym' => 'PR'],
        7 => ['color' => 'green', 'label' => 'Collector Super Rare', 'acronym' => 'CSR'],
        8 => ['color' => 'amber', 'label' => 'Collector Ultra Rare', 'acronym' => 'CUR'],
        9 => ['color' => 'emerald', 'label' => 'Collector Promo Rare', 'acronym' => 'CPR'],
    ];


    /**
     * Create a new component instance.
     */
    public function __construct($rarity)
    {
        $this->rarity = $rarity;
        $rarityData = $this->rarityColors[$rarity] ?? ['color' => 'gray', 'label' => 'Unknown'];
        $this->color = $rarityData['color'];
        $this->label = $rarityData['label'];
        $this->acronym = $rarityData['acronym'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card-rarity');
    }
}
