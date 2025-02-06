<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        
        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <flux:heading size="lg">
            {{ __('Delete Account') }}
        </flux:heading>

        <flux:subheading>
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted, but old orders will stay without a connection to this account.') }}
        </flux:subheading>
    </header>

    <flux:spacer />

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger">{{ __('Delete Account') }}</flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Are you sure you want to delete your account?') }}</flux:heading>
            <flux:subheading>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please confirm you would like to permanently delete your account.') }}</flux:subheading>
        </div>

        <div class="flex justify-center gap-1">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="danger" wire:click="deleteUser" >{{ __('Delete Account') }}</flux:button>
        </div>
    </flux:modal>
</section>
