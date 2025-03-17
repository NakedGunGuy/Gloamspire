<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Http\Request;

class UserDetails extends Component
{
    public $userId;

    public function mount($userId, Request $request)
    {
        $this->userId = $userId;
    }

    public function getUserProperty()
    {
        return User::find($this->userId);
    }

    public function render()
    {
        $section = request()->segment(3) ?? 'listings';

        return view('livewire.user-details', [
            'user' => $this->getUserProperty(),
            'section' => $section,
        ])->layout('layouts.app');
    }
}

