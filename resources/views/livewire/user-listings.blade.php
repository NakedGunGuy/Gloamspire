<div>
    @if ($userListings->count())
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Set</flux:table.column>
            <flux:table.column>Collector Number</flux:table.column>
            <flux:table.column>Rarity</flux:table.column>
            <flux:table.column>Amount</flux:table.column>
            <flux:table.column>Price</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($userListings as $listing)
                <flux:table.row 
                    :key="$listing->id"
                    class="{{ $listing->user_id === auth()->id() ? 'opacity-50' : '' }}"
                >
                    <flux:table.cell class="flex items-center gap-3 font-bold">
                        <flux:modal.trigger name="image-detail{{$listing->edition->id}}">
                            <flux:avatar size="xs" src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $listing->edition->slug }}.jpg" />
                        </flux:modal.trigger>
                        {{ $listing->edition->card->name }}
                        <flux:modal name="image-detail{{$listing->edition->id}}" class="md:w-96 space-y-6">
                            <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $listing->edition->slug }}.jpg">
                        </flux:modal>
                    </flux:table.cell>

                    <flux:table.cell>{{ $listing->edition->set->prefix ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $listing->edition->collector_number ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell><x-card-rarity :rarity="$listing->edition->rarity" /></flux:table.cell>
                    <flux:table.cell>
                        {{ $listing->card_count ?? 'N/A' }}
                        @if($listing->user_id === auth()->id() && $listing->cart_count_sum)
                            <span class="opacity-50">+ {{ $listing->cart_count_sum }}</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $listing->price . ' €' ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                            <flux:menu>
                                @if($listing->user_id === auth()->id())
                                    <flux:modal.trigger name="add-to-listing-{{ $listing->id }}-delete">
                                        <flux:menu.item variant="danger" icon="trash">Remove</flux:menu.item>
                                    </flux:modal.trigger>
                                @else
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
    <flux:heading class="mt-10">No listings :(</flux:heading>
    @endif
</div>
