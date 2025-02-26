<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('/assets/logo.svg') }}">
        <title>{{ config('app.name', 'Gloamspire') }}</title>

    
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/font.css', 'resources/js/app.js'])
        @fluxAppearance
        @livewireStyles
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 dark">
    
<flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <flux:brand href="/" logo="/assets/logo.svg" name="Gloamspire" class="px-2" />

    <!--<flux:input variant="filled" placeholder="Search..." icon="magnifying-glass" />-->

    <flux:navlist variant="outline">
    
        <flux:navlist.item 
        icon="home" 
        href="{{ route('home') }}" 
        :current="request()->routeIs('home')"
        wire:navigate
        >
            Home
        </flux:navlist.item>

        <flux:navlist.item 
        icon="building-storefront" 
        href="{{ route('listings') }}" 
        :current="request()->routeIs('listings')"
        wire:navigate
        >
            Listings
        </flux:navlist.item>

        <flux:navlist.item 
            icon="book-open" 
            href="{{ route('cards') }}" 
            :current="request()->routeIs('cards')"
            wire:navigate
        >
            Cards
        </flux:navlist.item>

        <flux:navlist.item 
            icon="user-group" 
            href="{{ route('users') }}"
            :current="request()->routeIs('users')"
            wire:navigate
        >
            Users
        </flux:navlist.item>

        @auth
        <flux:navmenu.separator />

        <flux:navlist.item 
            icon="shopping-cart" 
            href="{{ route('cart') }}"
            :current="request()->routeIs('cart')"
            wire:navigate
        >
            Cart
        </flux:navlist.item>

        <flux:navlist.item 
            icon="shopping-bag" 
            href="{{ route('order') }}"
            :current="request()->routeIs('order')"
            wire:navigate
        >
            Orders
        </flux:navlist.item>

        <flux:navlist.item 
            icon="heart" 
            href="{{ route('wishlist') }}"
            :current="request()->routeIs('wishlist')"
            wire:navigate
        >
            Wishlist
        </flux:navlist.item>
        @endauth

        <!--<flux:navlist.item icon="inbox" badge="12" href="#" current="{{ request()->segment(1) == 'dashboard' ? 'current' : '' }}">Inbox</flux:navlist.item>
        <flux:navlist.item icon="document-text" href="#" current="{{ request()->segment(1) == 'dashboard' ? 'current' : '' }}">Documents</flux:navlist.item>
        <flux:navlist.item icon="calendar" href="#" current="{{ request()->segment(1) == 'dashboard' ? 'current' : '' }}">Calendar</flux:navlist.item>-->

        <!--<flux:navlist.group expandable heading="Favorites" class="hidden lg:grid">
            <flux:navlist.item href="#">Marketing site</flux:navlist.item>
            <flux:navlist.item href="#">Android app</flux:navlist.item>
            <flux:navlist.item href="#">Brand guidelines</flux:navlist.item>
        </flux:navlist.group>-->
    </flux:navlist>

    <flux:spacer />
    @auth
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:profile avatar="{{ auth()->user()->avatar }}" name="{{ auth()->user()->name }}" />

        <flux:navmenu>
            <flux:navmenu.item href="{{ route('profile') }}" icon="user">Profile</flux:navmenu.item>
            <flux:navmenu.separator />
            <livewire:profile.logout-button />
        </flux:navmenu>
    </flux:dropdown>
    @else
    <flux:button href="{{ route('login') }}">Log in</flux:button>
    @endauth
</flux:sidebar>

<flux:header class="block! bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:navbar class="lg:hidden w-full">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        @auth
        <flux:dropdown position="top" align="start">
            <flux:profile avatar="{{ auth()->user()->avatar }}" name="{{ auth()->user()->name }}" />

            <flux:navmenu>
                <flux:navmenu.item href="{{ route('profile') }}" icon="user">Profile</flux:navmenu.item>
                <flux:navmenu.separator />
                <livewire:profile.logout-button />
            </flux:navmenu>
        </flux:dropdown>
        @else
        <flux:button href="{{ route('login') }}">Log in</flux:button>
        @endauth
    </flux:navbar>

    @if (isset($header))
        {{ $header }}
    @endif

</flux:header>

    <flux:main>
        {{ $slot }}
    </flux:main>

    @persist('toast')
        <flux:toast />
    @endpersist
    @fluxScripts
    @livewireScripts
</body>
</html>