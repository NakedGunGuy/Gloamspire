<x-app-layout>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Home') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    
    <flux:heading size="xl" level="1">Good afternoon</flux:heading>

    <flux:subheading size="lg" class="mb-6">Here's what's new today</flux:subheading>

    <flux:separator variant="subtle" />
</x-app-layout>
