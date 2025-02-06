<x-app-layout>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Cards') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    <livewire:card-filter />

</x-app-layout>
