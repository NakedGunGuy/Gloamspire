<flux:navbar scrollable >
    <flux:navbar.item href="{{ route('profile') }}" :current="request()->routeIs('profile')">Profile information</flux:navbar.item>
    <flux:navbar.item href="{{ route('personal-listings') }}" :current="request()->routeIs('personal-listings')">Listings</flux:navbar.item>
    <flux:navbar.item href="{{ route('cart') }}" :current="request()->routeIs('cart')">Cart</flux:navbar.item>
    <flux:navbar.item href="{{ route('order') }}" :current="request()->routeIs('order')">Orders</flux:navbar.item>
    <flux:navbar.item href="{{ route('wishlist') }}" :current="request()->routeIs('wishlist')">Wishlist</flux:navbar.item>
</flux:navbar>