<x-app-layout>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Listings') }}</flux:heading>
            <flux:spacer />
            @auth
            <!-- <livewire:import-listings /> -->
            @endauth
        </flux:navbar>
    </x-slot>

    <livewire:listing-filter />

</x-app-layout>
