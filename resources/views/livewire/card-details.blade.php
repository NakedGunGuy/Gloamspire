<div>
    <x-slot name="header">
        <flux:navbar scrollable>
            <flux:heading size="xl">{{ __('Card Details') }}</flux:heading>
        </flux:navbar>
    </x-slot>

    <div class="flex flex-col w-full gap-3">
        <flux:card class="!p-4 flex flex-col justify-between sm:items-center sm:flex-row gap-4">
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
            <flux:card class="!p-4 flex flex-col gap-3">
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

                    <div class="flex">
                        <flux:spacer />

                        <flux:button type="submit" variant="primary" wire:click="addToWishlist({{ $card->currentEdition->id }})">Save</flux:button>
                    </div>
                </flux:modal>
            </flux:card>
            <flux:card class="!p-4 flex-grow w-full flex">
                @if ($salesData->isNotEmpty())
                    <canvas class="!w-full flex-grow" id="salesChart"></canvas>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const salesData = @json($salesData);

                            const dates = salesData.map(sale => new Date(sale.created_at).toLocaleDateString());
                            const prices = salesData.map(sale => sale.price);

                            const ctx = document.getElementById('salesChart').getContext('2d');

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: dates,
                                    datasets: [{
                                        label: 'Sales Price',
                                        data: prices,
                                        borderColor: 'rgb(194, 0, 65)',
                                        backgroundColor: 'rgb(194, 0, 65)',
                                        borderWidth: 2,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            labels: {
                                                color: 'white' // 🟢 Make legend text white
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            ticks: {
                                                color: 'white' // 🟢 Make x-axis labels white
                                            }
                                        },
                                        y: {
                                            ticks: {
                                                color: 'white' // 🟢 Make y-axis labels white
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                @else
                    <flux:heading>No completed sales data available.</flux:heading>
                @endif
            </flux:card>
        </div>
        <div class="flex">
            <flux:card class="flex-grow w-full">
                @if ($listings->isNotEmpty())
                <flux:heading size="lg" >Current listings</flux:heading>
                <flux:table :paginate="$listings">
                    <flux:columns>
                        <flux:column>Price</flux:column>
                        <flux:column>Count</flux:column>
                        <flux:column>User</flux:column>
                        <flux:column>Updated at</flux:column>
                        <flux:column></flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach ($listings as $listing)
                        <flux:row>
                            <flux:cell variant="strong">{{ $listing->price }} €</flux:cell>
                            <flux:cell>{{ $listing->card_count }}</flux:cell>
                            <flux:cell>{{ $listing->user->name }}</flux:cell>
                            <flux:cell>{{ $listing->updated_at->format('d.m.Y H:i:s') }}</flux:cell>
                            <flux:cell>
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
                            </flux:cell>
                        </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
                @else
                    <flux:heading>No listings found.</flux:heading>
                @endif
            </flux:card>
        </div>
    </div>
</div>