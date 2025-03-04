<div>
    <flux:accordion transition class="flex flex-col gap-3">
        @foreach($orders as $orderId => $orderGroup)
        @php
            // Calculate the total price for this order
            $totalPrice = $orderGroup->first()->order_items->sum(function ($orderItem) {
                return $orderItem->listing->price * $orderItem->amount;
            });

            $user = $orderGroup->first()->user; // The user who owns the order
            $listingUser = $orderGroup->first()->order_items->first()?->listing->user; // The user associated with the listing

            $status = $orderGroup->first()->status; // Get the status of the order

            // Define status-specific button styles
            $statusColors = [
                'pending' => 'bg-orange-500!',
                'sent' => 'bg-blue-500!',
                'completed' => 'bg-green-500!',
                'canceled' => 'bg-red-500!',
            ];

            $statusColor = $statusColors[$status] ?? 'bg-gray-500!'; // Default color

            $canMarkAsSent = $orderGroup->first()->order_items->first()?->listing->user_id === auth()->id() && $status === 'pending';
            $canMarkAsCompleted = $user->id === auth()->id() && $status === 'sent';
            $canMarkAsCanceled = $orderGroup->first()->order_items->first()?->listing->user_id === auth()->id() && in_array($status, ['pending', 'sent']);
            $isFinalStatus = in_array($status, ['completed', 'canceled']);
        @endphp
        <flux:card class="flex gap-3 flex-col md:flex-row">
            <flux:accordion.item expanded class="w-full h-fit">
                <flux:accordion.heading>
                    <div class="inline-flex flex-wrap gap-2 items-center">
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
                    </div>
                </flux:accordion.heading>
                <flux:accordion.content>
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column class="w-9/12 min-w-[150px]">Name</flux:table.column>
                            <flux:table.column class="w-1/12 min-w-[100px]">Set</flux:table.column>
                            <flux:table.column class="w-1/12 min-w-[100px]">Collector Number</flux:table.column>
                            <flux:table.column class="w-1/12 min-w-[100px]">Rarity</flux:table.column>
                            <flux:table.column class="w-1/12 min-w-[100px]">Foil</flux:table.column>
                            <flux:table.column class="w-1/12 min-w-[100px]">Amount</flux:table.column>
                            <flux:table.column class="w-1/12 min-w-[100px]">Price</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach($orderGroup->first()->order_items as $orderItem)
                            <flux:table.row :key="$orderItem->id">
                                <flux:table.cell class="flex items-center gap-3 font-bold">
                                    <flux:modal.trigger name="image-detail{{$orderItem->listing->edition->id}}">
                                        <flux:avatar size="xs" src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $orderItem->listing->edition->slug }}.jpg" />
                                    </flux:modal.trigger>
                                    {{ $orderItem->listing->edition->card->name }}
                                    <flux:modal name="image-detail{{$orderItem->listing->edition->id}}" class="md:w-96 space-y-6">
                                        <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $orderItem->listing->edition->slug }}.jpg">
                                    </flux:modal>
                                </flux:table.cell>
                                <flux:table.cell>{{ $orderItem->listing->edition->set->prefix ?? 'N/A' }}</flux:table.cell>
                                <flux:table.cell>{{ $orderItem->listing->edition->collector_number ?? 'N/A' }}</flux:table.cell>
                                <flux:table.cell><x-card-rarity :rarity="$orderItem->listing->edition->rarity" /></flux:table.cell>
                                <flux:table.cell><x-card-foil :is_foil="$orderItem->listing->is_foil" /></flux:table.cell>
                                <flux:table.cell variant="strong">{{ $orderItem->amount }}</flux:table.cell>
                                <flux:table.cell variant="strong">{{ $orderItem->listing->price }}</flux:table.cell>
                            </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <div class="flex flex-col gap-2.5">
                <flux:button.group>
                    <flux:button class="{{ $statusColor }}" disabled>
                        {{ ucfirst($status) }}
                    </flux:button>
                    <flux:button href="{{ route('order.details', $orderId) }}" icon="arrow-top-right-on-square"></flux:button>
                    <flux:dropdown>
                        <flux:button icon-trailing="chevron-down"></flux:button>

                        <flux:menu>
                            @if ($canMarkAsSent || $canMarkAsCompleted || $canMarkAsCanceled)
                            <flux:menu.group heading="Status">
                                @if ($canMarkAsSent && !$isFinalStatus)
                                    <flux:menu.item wire:click="updateStatus('sent', {{ $orderId }})" class="{{ $statusColors['sent'] }}">Sent</flux:menu.item>
                                @endif
                                @if ($canMarkAsCompleted && !$isFinalStatus)
                                    <flux:menu.item wire:click="updateStatus('completed', {{ $orderId }})" class="hover:{{ $statusColors['completed'] }}">Completed</flux:menu.item>
                                @endif
                                @if ($canMarkAsCanceled && !$isFinalStatus)
                                    <flux:menu.item wire:click="updateStatus('canceled', {{ $orderId }})" class="{{ $statusColors['canceled'] }}">Canceled</flux:menu.item>
                                @endif
                            </flux:menu.group>
                            @endif

                            <flux:menu.group>
                                <flux:menu.item icon="chat-bubble-oval-left-ellipsis" href="discord://discordapp.com/users/{{ $listingUser?->discord_id }}">Contact seller</flux:menu.item>
                            </flux:menu.group>
                        </flux:menu>
                    </flux:dropdown>
                </flux:button.group>
                <flux:heading>Summary</flux:heading>
                <flux:separator />
                <flux:heading size="lg">{{ number_format($totalPrice, 2) }}</flux:heading>
            </div>
        </flux:card>
        @endforeach
    </flux:accordion>
</div>
