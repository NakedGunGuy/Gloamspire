<div>
    <flux:card class="flex flex-wrap items-center justify-between gap-2 md:gap-4">
        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-4">
            <flux:input 
                icon="magnifying-glass" 
                placeholder="Search name" 
                type="text" 
                wire:model.live="search"
            />

            <flux:select 
                variant="listbox" 
                multiple 
                searchable 
                wire:model.live="edition" 
                placeholder="Choose set..." 
                clear="close"
            >
                @foreach($sets as $set)
                    <flux:select.option value="{{ $set->id }}">{{ $set->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select 
                variant="listbox" 
                multiple 
                searchable 
                wire:model.live="class" 
                placeholder="Choose class..." 
                clear="close"
            >
                @foreach($classes as $class)
                    <flux:select.option value="{{ $class->id }}">{{ $class->value }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select 
                variant="listbox" 
                multiple 
                searchable 
                wire:model.live="type" 
                placeholder="Choose type..." 
                clear="close"
            >
                @foreach($types as $type)
                    <flux:select.option value="{{ $type->id }}">{{ $type->value }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select 
                variant="listbox" 
                multiple 
                searchable 
                wire:model.live="subtype" 
                placeholder="Choose subtype..." 
                clear="close" 
            >
                @foreach($subtypes as $subtype)
                    <flux:select.option value="{{ $subtype->id }}">{{ $subtype->value }}</flux:select.option>
                @endforeach
            </flux:select>
            
        </div>
        <div class="flex flex-wrap justify-end gap-4 w-full md:w-fit">
            <flux:select 
                wire:model.live="perPage" 
                variant="listbox" 
                placeholder="Per Page" 
                class="min-w-[50px] max-w-[100px]"
            >
                @foreach($perPageOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:radio.group 
                wire:model.live="viewType" 
                variant="segmented" 
            >
                <flux:radio icon="list-bullet" value="list" />
                <flux:radio icon="squares-2x2" value="box" />
            </flux:radio.group>
        </div>
    </flux:card>

    <!--<flux:checkbox.group>-->
        @if($viewType === 'list')
        <flux:table :paginate="$cards">
            <flux:table.columns>
                <!--<flux:table.column class="w-[20px]"><flux:checkbox.all /></flux:table.column>-->
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'set_prefix'" :direction="$sortDirection" wire:click="sort('set_prefix')">Set</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'collector_number'" :direction="$sortDirection" wire:click="sort('collector_number')">Collector Number</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'rarity'" :direction="$sortDirection" wire:click="sort('rarity')">Rarity</flux:table.column>
                <flux:table.columns></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($cards as $card)
                    <flux:table.row :key="$card->id">
                        <!--<flux:table.cell class="w-[20px]"><flux:checkbox /></flux:table.cell>-->
                        <flux:table.cell class="flex items-center gap-3 font-bold">
                            <flux:modal.trigger name="image-detail{{$card->id}}">
                                <flux:avatar size="xs" src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $card->slug }}.jpg" />
                            </flux:modal.trigger>
                            <a href="{{ route('card.details', ['cardId' => $card->card_id]) }}?edition={{ $card->set->prefix }}">{{ $card->card->name }}</a>
                            <flux:modal name="image-detail{{$card->id}}" class="md:w-96 space-y-6">
                                <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $card->slug }}.jpg">
                            </flux:modal>
                        </flux:table.cell>

                        <flux:table.cell>{{ $card->set->prefix ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>{{ $card->collector_number ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell><x-card-rarity :rarity="$card->rarity" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    <flux:modal.trigger name="add-to-listing-{{ $card->id }}">
                                        <flux:menu.item icon="plus">Add to listing</flux:menu.item>
                                    </flux:modal.trigger>
                                    <flux:modal.trigger name="add-to-wishlist-{{ $card->id }}">
                                        <flux:menu.item icon="heart">Add to Wishlist</flux:menu.item>
                                    </flux:modal.trigger>
                                </flux:menu>
                            </flux:dropdown>
                            <flux:modal name="add-to-listing-{{ $card->id }}" class="md:w-96 space-y-6">
                                <div>
                                    <flux:heading size="lg">Add card to listing</flux:heading>
                                </div>

                                <flux:input name="card_count" type="number" placeholder="Amount" wire:model="card_count" />

                                <flux:input.group>
                                    <flux:input.group.prefix>EUR</flux:input.group.prefix>

                                    <flux:input name="price" type="number" placeholder="Price" min="0.00" max="100000.00" step="0.01" wire:model="price"/>
                                </flux:input.group>

                                <flux:checkbox wire:model="is_foil" label="Foil" />

                                <div class="flex">
                                    <flux:spacer />

                                    <flux:button type="submit" variant="primary" wire:click="addToListing({{ $card->id }})">Save</flux:button>
                                </div>
                            </flux:modal>
                            <flux:modal name="add-to-wishlist-{{ $card->id }}" class="md:w-96 space-y-6">
                                <div>
                                    <flux:heading size="lg">Add card to wishlist</flux:heading>
                                </div>

                                <flux:input name="card_count" type="number" placeholder="Amount" wire:model="card_count" />

                                <flux:checkbox wire:model="is_foil" label="Foil" />

                                <div class="flex">
                                    <flux:spacer />

                                    <flux:button type="submit" variant="primary" wire:click="addToWishlist({{ $card->id }})">Save</flux:button>
                                </div>
                            </flux:modal>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
        @elseif($viewType === 'box')
        <flux:table :paginate="$cards">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 my-7">
                @foreach($cards as $card)
                    <flux:card class="p-4! pt-2! flex flex-col items-center">
                        <flux:heading size="lg" class="mb-2">{{ $card->card->name }}</flux:heading>
                        <div class="overflow-hidden rounded-xl flex">
                            <flux:modal.trigger name="image-detail{{$card->id}}">
                                <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $card->slug }}.jpg" class="w-full"/>
                            </flux:modal.trigger>
                            <flux:modal name="image-detail{{$card->id}}" class="md:w-96 space-y-6">
                                <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $card->slug }}.jpg">
                            </flux:modal>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <div class="flex flex-wrap gap-1">
                                <flux:heading>{{ $card->set->prefix ?? 'N/A' }}</flux:heading>
                                <flux:heading>-</flux:heading>
                                <flux:heading>{{ $card->collector_number ?? 'N/A' }}</flux:heading>
                            </div>
                            <flux:separator vertical />
                            <flux:heading><x-card-rarity :rarity="$card->rarity" /></flux:heading>
                        </div>
                        <div class="w-full! mt-3">
                            <flux:button.group>
                                <flux:modal.trigger name="add-to-listing-{{ $card->id }}">
                                    <flux:button class="w-full!" icon="plus" variant="primary"></flux:button>
                                </flux:modal.trigger>
                                <flux:modal.trigger name="add-to-wishlist-{{ $card->id }}">
                                    <flux:button class="w-full!" icon="heart" variant="danger"></flux:button>
                                </flux:modal.trigger>
                            </flux:button.group>
                            <flux:modal name="add-to-listing-{{ $card->id }}" class="md:w-96 space-y-6">
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

                                    <flux:button type="submit" variant="primary" wire:click="addToListing({{ $card->id }})">Save</flux:button>
                                </div>
                            </flux:modal>
                            <flux:modal name="add-to-wishlist-{{ $card->id }}" class="md:w-96 space-y-6">
                                <div>
                                    <flux:heading size="lg">Add card to wishlist</flux:heading>
                                </div>

                                <flux:input name="card_count" type="number" placeholder="Amount" wire:model="card_count" />

                                <div class="flex">
                                    <flux:spacer />

                                    <flux:button type="submit" variant="primary" wire:click="addToWishlist({{ $card->id }})">Save</flux:button>
                                </div>
                            </flux:modal>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </flux:table>
        @endif
    </flux:checkbox.group>
</div>
