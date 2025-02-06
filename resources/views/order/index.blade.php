<x-app-layout>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Orders') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    <livewire:order-component />

</x-app-layout>
