<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Flux\Flux;

class UserDetails extends Component
{

    public $userId;
    public $tab = 'listings';

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function getUserProperty()
    {
        return User::find($this->userId);
    }

    public function render()
    {
        return view('livewire.user-details', [
            'user' => $this->getUserProperty(),
        ])->layout('layouts.app');
    }
}
