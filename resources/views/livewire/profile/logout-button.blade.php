<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Flux\Flux;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        Flux::toast(
            text: 'Logged out!',
            variant: 'success',
        );
        
        $this->redirect('/', navigate: true);
    }
}; ?>

<flux:menu.item wire:click="logout" icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>