<x-app-layout>
    <!--<x-slot name="header">
        <flux:navbar scrollable>
            <flux:navbar.item href="#" current>Dashboard</flux:navbar.item>
            <flux:navbar.item badge="32" href="#">Orders</flux:navbar.item>
            <flux:navbar.item href="#">Catalog</flux:navbar.item>
            <flux:navbar.item href="#">Configuration</flux:navbar.item>
        </flux:navbar>
    </x-slot>-->

    <x-slot name="header">
        <x-profile-nav />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark shadow-sm sm:rounded-lg dark:bg-zinc-900">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>
            
            <!-- <div class="p-4 sm:p-8 bg-white dark:bg-zinc-900 shadow-sm sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div> -->
        </div>
    </div>
</x-app-layout>
