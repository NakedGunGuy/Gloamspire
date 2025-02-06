<x-app-layout>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Cart') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    <livewire:cart-component />

</x-app-layout>
