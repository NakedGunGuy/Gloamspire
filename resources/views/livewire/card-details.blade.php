<div>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Card Details') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    <div class="flex flex-col w-full gap-3">
        <flux:card class="p-4! flex flex-col justify-between sm:items-center sm:flex-row gap-4">
            <flux:heading size="xl">{{ $card->name }}</flux:heading>
            <div>
            @foreach ($this->editions as $edition)
                @if ($edition->id != $selectedEditionId)
                <flux:button 
                    href="{{ route('card.details', ['cardId' => $this->cardId, 'edition' => $edition->set->prefix]) }}"
                    variant="subtle"
                >
                    {{ $edition->set->prefix }}
                </flux:button>
                @else
                <flux:button 
                    href="{{ route('card.details', ['cardId' => $this->cardId, 'edition' => $edition->set->prefix]) }}"
                    variant="primary"
                >
                    {{ $edition->set->prefix }}
                </flux:button>
                @endif
            @endforeach
            </div>
        </flux:card>
        <div class="flex w-full gap-3 flex-col xl:flex-row">
            <flux:card class="p-4! flex flex-col gap-3">
                <div class="overflow-hidden rounded-xl flex">
                    <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $card->currentEdition->slug }}.jpg" width="400px"/>
                </div>
                <flux:button.group class="w-full">
                    <flux:modal.trigger name="add-to-listing-{{ $card->currentEdition->id }}">
                        <flux:button icon="plus" variant="primary" class="w-full">Add to listing</flux:button>
                    </flux:modal.trigger>
                    <flux:modal.trigger name="add-to-wishlist-{{ $card->currentEdition->id }}">
                        <flux:button icon="heart" variant="danger" class="w-full">Add to Wishlist</flux:button>
                    </flux:modal.trigger>
                </flux:button.group>
                <flux:modal name="add-to-listing-{{ $card->currentEdition->id }}" class="md:w-96 space-y-6">
                    <div>
                        <flux:heading size="lg">Add card to listing</flux:heading>
                    </div>

                    <flux:input name="card_count" type="number" placeholder="Amount" wire:model="card_count" />

                    <flux:input.group>
                        <flux:input.group.prefix>EUR</flux:input.group.prefix>

                        <flux:input name="price" type="number" placeholder="Price" min="0.00" max="100000.00" step="0.01" wire:model="price"/>
                    </flux:input.group>

                    <flux:checkbox wire:model="isFoil" label="Foil" />

                    <div class="flex">
                        <flux:spacer />

                        <flux:button type="submit" variant="primary" wire:click="addToListing({{ $card->currentEdition->id }})">Save</flux:button>
                    </div>
                </flux:modal>
                <flux:modal name="add-to-wishlist-{{ $card->currentEdition->id }}" class="md:w-96 space-y-6">
                    <div>
                        <flux:heading size="lg">Add card to wishlist</flux:heading>
                    </div>

                    <flux:input name="card_count" type="number" placeholder="Amount" wire:model="card_count" />

                    <flux:checkbox wire:model="isFoil" label="Foil" />

                    <div class="flex">
                        <flux:spacer />

                        <flux:button type="submit" variant="primary" wire:click="addToWishlist({{ $card->currentEdition->id }})">Save</flux:button>
                    </div>
                </flux:modal>
            </flux:card>
            <flux:card class="p-4! grow w-full flex">
                @if ($salesData)
                    <flux:chart class="grid gap-6 w-full" wire:model="salesData">
                        <flux:chart.summary class="flex gap-12">
                            <div>
                                <flux:subheading>Price</flux:subheading>

                                <flux:heading size="xl">
                                    <flux:chart.summary.value field="price" :format="['style' => 'currency', 'currency' => 'EUR']" />
                                </flux:heading>
                            </div>

                        </flux:chart.summary>

                        <flux:chart.viewport class="aspect-[3/1]">
                            <flux:chart.svg>
                                <flux:chart.line field="price" class="text-sky-500 dark:text-sky-400" curve="none" />

                                <flux:chart.axis axis="x" field="date" :format=" ['day' => '2-digit', 'month' => '2-digit', 'year' => '2-digit', 'timeZone' => 'UTC']">
                                    <flux:chart.axis.grid />
                                    <flux:chart.axis.tick />
                                    <flux:chart.axis.line />
                                </flux:chart.axis>

                                <flux:chart.axis axis="y">
                                    <flux:chart.axis.tick />
                                </flux:chart.axis>

                                <flux:chart.cursor />
                            </flux:chart.svg>
                        </flux:chart.viewport>
                    </flux:chart>
                @else
                    <flux:heading>No completed sales data available.</flux:heading>
                @endif
            </flux:card>
        </div>
        <div class="flex">
            <flux:card class="grow w-full">
                @if ($listings->isNotEmpty())
                <flux:heading size="lg" >Current listings</flux:heading>
                <flux:table :paginate="$listings">
                    <flux:table.columns>
                        <flux:table.column>Price</flux:table.column>
                        <flux:table.column>Count</flux:table.column>
                        <flux:table.column>User</flux:table.column>
                        <flux:table.column>Updated at</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($listings as $listing)
                        <flux:table.row>
                            <flux:table.cell variant="strong">{{ $listing->price }} €</flux:table.cell>
                            <flux:table.cell>{{ $listing->card_count }}</flux:table.cell>
                            <flux:table.cell>{{ $listing->user->name }}</flux:table.cell>
                            <flux:table.cell>{{ $listing->updated_at->format('d.m.Y H:i:s') }}</flux:table.cell>
                            <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    @if($listing->user_id === auth()->id())
                                        <flux:modal.trigger name="add-to-listing-{{ $listing->id }}-delete">
                                            <flux:menu.item variant="danger" icon="trash">Remove</flux:menu.item>
                                        </flux:modal.trigger>
                                    @else
                                        <!-- Options for other users -->
                                        <flux:modal.trigger name="add-to-cart-{{ $listing->id }}">
                                            <flux:menu.item icon="shopping-cart">Add to cart</flux:menu.item>
                                        </flux:modal.trigger>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                            @if($listing->user_id === auth()->id())
                            <flux:modal name="add-to-listing-{{ $listing->id }}-delete" class="md:w-96 space-y-6">
                                <div>
                                    <flux:heading size="xl">Listing (Amount: {{ $listing->card_count ?? 'N/A' }}) will be deleted</flux:heading>
                                </div>
                                <flux:spacer />
                                <div class="flex justify-center gap-1">
                                    <flux:modal.close>
                                        <flux:button variant="ghost">Cancel</flux:button>
                                    </flux:modal.close>

                                    <flux:button type="submit" variant="danger" wire:click="removeListing({{ $listing->id }})">Delete</flux:button>
                                </div>
                            </flux:modal>
                            @else
                            <flux:modal name="add-to-cart-{{ $listing->id }}" class="md:w-96 space-y-6">
                                <div>
                                    <flux:heading size="lg">Add to cart</flux:heading>
                                </div>

                                <flux:input.group>
                                    <flux:input.group.prefix>{{ $listing->card_count }}</flux:input.group.prefix>

                                    <flux:input name="amount" type="number" placeholder="Amount" wire:model="amount" min="1" max="{{ $listing->card_count }}"/>
                                </flux:input.group>

                                <div class="flex">
                                    <flux:spacer />

                                    <flux:button type="submit" variant="primary" wire:click="addToCart({{ $listing->id }})">Add</flux:button>
                                </div>
                            </flux:modal>
                            @endif
                            </flux:table.cell>
                        </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
                @else
                    <flux:heading>No listings found.</flux:heading>
                @endif
            </flux:card>
        </div>
    </div>
</div>