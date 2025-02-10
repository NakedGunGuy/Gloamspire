<x-app-layout>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Wishlist') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    <livewire:wishlist-component />

</x-app-layout>
