<div>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('User Details') }}</flux:heading>
        </flux:navbar>
    </x-slot>
    @if ($user)
    <flux:card class="flex items-start gap-4 flex-col sm:flex-row sm:items-center">
        <!-- User Avatar -->
        @if ($user->avatar)
        <flux:avatar src="{{ $user->avatar }}" name="{{ $user->name }}" />
        @endif
        <div class="flex items-center gap-4">
            <flux:heading size="xl" class="break-all">{{ $user->name }}</flux:heading>
            <x-country-flag :country-code="$user->country" />
        </div>
        <flux:spacer />
        <flux:button icon="chat-bubble-oval-left-ellipsis" href="discord://discordapp.com/users/{{ $user?->discord_id }}">Contact user</flux:button>
    </flux:card>

    <flux:tab.group class="mt-4">
        <flux:tabs wire:model="tab">
            <flux:tab name="listings">Listings</flux:tab>
            <flux:tab name="wishlist">Wishlist</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="listings">
            <livewire:user-listings :userId="$userId" />
        </flux:tab.panel>
        <flux:tab.panel name="wishlist">
            <livewire:wishlist-component :userId="$userId" />
        </flux:tab.panel>
    </flux:tab.group>
    
    @else
    <p>User not found.</p>
    @endif
</div>
