<x-app-layout>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Home') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    
    <flux:heading size="xl" level="1">Welcome to Gloamspire</flux:heading>

    <flux:subheading size="lg" class="mb-6 max-w-2xl">This a passion project (still in development) born out of a love for collectible cards and the community that surrounds them. Built in my free time, this platform is a labor of dedication, combining technology and trading to create something special for collectors like you.

</br></br>This journey wouldn’t have been possible without the incredible support and encouragement from my friends at Cardpoint. Their insights, feedback, and enthusiasm have played a huge role in shaping Gloamspire into what it is today.

Thank you for being here, and I hope you enjoy the experience!</flux:subheading>

    <flux:separator variant="subtle" />
</x-app-layout>
