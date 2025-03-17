<div>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('User Details') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    @if ($user)
    <flux:card class="flex items-start gap-4 flex-col sm:flex-row sm:items-center">
        @if ($user->avatar)
        <flux:avatar src="{{ $user->avatar }}" name="{{ $user->name }}" />
        @endif
        <div class="flex items-center gap-4">
            <flux:heading size="xl" class="break-all">{{ $user->name }}</flux:heading>
            <x-country-flag :country-code="$user->country" />
        </div>
        <flux:spacer />
        <flux:button icon="chat-bubble-oval-left-ellipsis" href="discord://discordapp.com/users/{{ $user?->discord_id }}">
            Contact user
        </flux:button>
    </flux:card>

    <!-- Navigation -->
    <flux:navbar class="mt-4">
        <flux:navbar.item href="{{ route('user.details', ['userId' => $user->id, 'section' => 'listings']) }}" :current="$section === 'listings'">
            Listings
        </flux:navbar.item>
        <flux:navbar.item href="{{ route('user.details', ['userId' => $user->id, 'section' => 'wishlist']) }}" :current="$section === 'wishlist'">
            Wishlist
        </flux:navbar.item>
    </flux:navbar>

    <flux:separator />

    <div class="mt-4">
        @if ($section === 'wishlist')
            <livewire:wishlist-component :userId="$user->id" />
        @else
            <livewire:user-listings :userId="$user->id" />
        @endif
    </div>

    @else
    <p>User not found.</p>
    @endif
</div>
