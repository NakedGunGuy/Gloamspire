<div>
    <flux:subheading class="mb-5 text-center">Listings will be deleted a day after added to cart!</flux:subheading>
    <flux:accordion transition class="flex flex-col gap-3">
        @foreach($groupedCart as $listingUserId => $cartItems)
        @php
            // Calculate the total price for the user's cart items
            $totalPrice = $cartItems->sum(function ($cartItem) {
                return $cartItem->listing->price * $cartItem->amount;
            });
        @endphp
        <flux:card class="flex gap-3 flex-col md:flex-row">
            <flux:accordion.item expanded class="w-full h-fit">
                <flux:accordion.heading>
                    <div class="inline-flex flex-wrap gap-2 items-center">
                        <span>Listing by</span>
                        <flux:badge>
                            {{ $cartItems->first()->listing->user->name ?? 'Unknown User' }}
                        </flux:badge>
                        <flux:tooltip content="{{ $countries[$cartItems->first()->listing->user->country]['name'] ?? 'N/A' }}">
                            <div>{{ $countries[$cartItems->first()->listing->user->country]['flag'] ?? 'N/A' }}</div>
                        </flux:tooltip>
                    </div>
                </flux:accordion.heading>
                <flux:accordion.content>
                    <flux:table>
                        <flux:columns>
                            <flux:column class="w-9/12 min-w-[150px]">Name</flux:column>
                            <flux:column class="w-1/12 min-w-[100px]">Amount</flux:column>
                            <flux:column class="w-1/12 min-w-[100px]">Price</flux:column>
                            <flux:column class="w-1/12 min-w-[50px]"></flux:column>
                        </flux:columns>

                        <flux:rows>
                            @foreach($cartItems as $cartItem)
                            <flux:row :key="$cartItem->id">
                                <flux:cell class="flex items-center gap-3 font-bold">
                                    <flux:modal.trigger name="image-detail{{$cartItem->listing->edition->id}}">
                                        <flux:avatar size="xs" src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $cartItem->listing->edition->slug }}.jpg" />
                                    </flux:modal.trigger>
                                    {{ $cartItem->listing->edition->card->name }}
                                    <flux:modal name="image-detail{{$cartItem->listing->edition->id}}" class="md:w-96 space-y-6">
                                        <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $cartItem->listing->edition->slug }}.jpg">
                                    </flux:modal>
                                </flux:cell>
                                <flux:cell variant="strong">{{ $cartItem->amount }}</flux:cell>
                                <flux:cell variant="strong">{{ $cartItem->listing->price }}</flux:cell>
                                <flux:cell>
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                                    <flux:menu>
                                        <flux:modal.trigger name="cart-item-{{ $cartItem->id }}-delete">
                                            <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                                        </flux:modal.trigger>
                                    </flux:menu>
                                </flux:dropdown>
                                <flux:modal name="cart-item-{{ $cartItem->id }}-delete" class="md:w-96 space-y-6">
                                    <div>
                                        <flux:heading size="xl">Delete this item?</flux:heading>
                                    </div>
                                    <flux:spacer />
                                    <div class="flex justify-center gap-1">
                                        <flux:modal.close>
                                            <flux:button variant="ghost">Cancel</flux:button>
                                        </flux:modal.close>

                                        <flux:button type="submit" variant="danger" wire:click="removeSingleCartItem({{ $cartItem->id }})">
                                            Delete
                                        </flux:button>
                                    </div>
                                </flux:modal>
                                </flux:cell>
                            </flux:row>
                            @endforeach
                        </flux:rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <div class="flex flex-col gap-2.5">
                <flux:button.group>
                    <flux:button variant="primary" icon="check" wire:click="buyCartItem({{ $cartItems->first()->listing->user->id }})">Buy</flux:button>
                    <flux:dropdown>
                        <flux:button icon-trailing="chevron-down"></flux:button>

                        <flux:menu>
                            <flux:menu.item icon="chat-bubble-oval-left-ellipsis" href="discord://discordapp.com/users/{{ $cartItems->first()->listing->user->discord_id }}">Contact seller</flux:menu.item>

                            <flux:menu.separator />

                            <flux:modal.trigger name="cart-item-{{ $cartItems->first()->listing->user->id }}-delete">
                                <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                            </flux:modal.trigger>
                        </flux:menu>
                    </flux:dropdown>
                </flux:button.group>
                <flux:heading>Summary</flux:heading>
                <flux:separator />
                <flux:heading size="lg">{{ number_format($totalPrice, 2) }}</flux:heading>
                <flux:modal name="cart-item-{{ $cartItems->first()->listing->user->id }}-delete" class="md:w-96 space-y-6">
                    <div>
                        <flux:heading size="xl">Cart item will be deleted</flux:heading>
                    </div>
                    <flux:spacer />
                    <div class="flex justify-center gap-1">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>

                        <flux:button type="submit" variant="danger" wire:click="removeCartItem({{ $cartItems->first()->listing->user->id }})">Delete</flux:button>
                    </div>
                </flux:modal>
            </div>
        </flux:card>
        @endforeach
    </flux:accordion>
</div>
