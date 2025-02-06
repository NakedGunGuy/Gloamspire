<div>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Order Details') }}</flux:heading>
        </flux:navbar>
    </x-slot>
    
    
    @php
        // Calculate the total price for this order
        $totalPrice = $order->order_items->sum(function ($orderItem) {
            return $orderItem->listing->price * $orderItem->amount;
        });

        $user = $order->user; // The user who owns the order
        $listingUser = $order->order_items->first()?->listing->user; // The user associated with the listing

        $status = $order->status; // Get the status of the order

        // Define status-specific button styles
        $statusColors = [
            'pending' => '!bg-orange-500',
            'sent' => '!bg-blue-500',
            'completed' => '!bg-green-500',
            'canceled' => '!bg-red-500',
        ];

        $statusColor = $statusColors[$status] ?? '!bg-gray-500'; // Default color

        $canMarkAsSent = $order->order_items->first()?->listing->user_id === auth()->id() && $status === 'pending';
        $canMarkAsCompleted = $user->id === auth()->id() && $status === 'sent';
        $canMarkAsCanceled = $order->order_items->first()?->listing->user_id === auth()->id() && in_array($status, ['pending', 'sent']);
        $isFinalStatus = in_array($status, ['completed', 'canceled']);
    @endphp

    <flux:card class="flex justify-between">
        <flux:heading class="inline-flex flex-wrap gap-2 items-center">
            <span>From</span>
            <flux:badge>
                {{ $listingUser?->name ?? 'Unknown Listing User' }}
            </flux:badge>
            <x-country-flag :country-code="$listingUser?->country" />

            <flux:icon.chevron-double-right />
            
            <span>Order by</span>
            <flux:badge>
                {{ $user->name ?? 'Unknown User' }}
            </flux:badge>
            <x-country-flag :country-code="$user->country" />
        </flux:heading>
        <flux:button.group>
            <flux:button class="{{ $statusColor }}" disabled>
                {{ ucfirst($status) }}
            </flux:button>
            <flux:dropdown>
                <flux:button icon-trailing="chevron-down"></flux:button>

                <flux:menu>
                    @if ($canMarkAsSent || $canMarkAsCompleted || $canMarkAsCanceled)
                    <flux:menu.group heading="Status">
                        @if ($canMarkAsSent && !$isFinalStatus)
                            <flux:menu.item wire:click="updateStatus('sent')" class="{{ $statusColors['sent'] }}">Sent</flux:menu.item>
                        @endif
                        @if ($canMarkAsCompleted && !$isFinalStatus)
                            <flux:menu.item wire:click="updateStatus('completed')" class="hover:{{ $statusColors['completed'] }}">Completed</flux:menu.item>
                        @endif
                        @if ($canMarkAsCanceled && !$isFinalStatus)
                            <flux:menu.item wire:click="updateStatus('canceled')" class="{{ $statusColors['canceled'] }}">Canceled</flux:menu.item>
                        @endif
                    </flux:menu.group>
                    @endif

                    <flux:menu.group>
                        <flux:menu.item icon="chat-bubble-oval-left-ellipsis" href="discord://discordapp.com/users/{{ $listingUser?->discord_id }}">Contact seller</flux:menu.item>
                    </flux:menu.group>
                </flux:menu>
            </flux:dropdown>
        </flux:button.group>
    </flux:card>
    <flux:table>
        <flux:columns>
            <flux:column class="w-9/12 min-w-[150px]">Name</flux:column>
            <flux:column class="w-1/12 min-w-[100px]">Amount</flux:column>
            <flux:column class="w-1/12 min-w-[100px]">Price</flux:column>
        </flux:columns>

        <flux:rows>
            @foreach ($order->order_items as $orderItem)
            <flux:row :key="$orderItem->id">
                <flux:cell class="flex items-center gap-3 font-bold">
                    <flux:modal.trigger name="image-detail{{$orderItem->listing->edition->id}}">
                        <flux:avatar size="xs" src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $orderItem->listing->edition->slug }}.jpg" />
                    </flux:modal.trigger>
                    {{ $orderItem->listing->edition->card->name }}
                    <flux:modal name="image-detail{{$orderItem->listing->edition->id}}" class="md:w-96 space-y-6">
                        <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $orderItem->listing->edition->slug }}.jpg">
                    </flux:modal>
                </flux:cell>
                <flux:cell variant="strong">{{ $orderItem->amount }}</flux:cell>
                <flux:cell variant="strong">{{ $orderItem->listing->price }}</flux:cell>
            </flux:row>
            @endforeach
        </flux:rows>
    </flux:table>
    <flux:card>
        <flux:heading size="lg" class="text-right">Summary: {{ $totalPrice }}</flux:heading>
    </flux:card>
</div>
