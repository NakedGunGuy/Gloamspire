<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col-reverse items-end sm:items-center sm:flex-row gap-2 sm:gap-4 mb-4 sm:mb-0">
            <x-profile-nav />
            <flux:spacer />
            @auth
            <livewire:import-listings />
            @endauth
        </div>
    </x-slot>

    <livewire:listings-component />

</x-app-layout>
