<x-app-layout>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    <livewire:user-filter />

</x-app-layout>
