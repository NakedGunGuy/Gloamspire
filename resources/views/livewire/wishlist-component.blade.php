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

            <flux:checkbox wire:model.live="isFoil" label="Foil" />

        </div>
        <div class="flex flex-wrap justify-end gap-2 w-full md:w-fit md:gap-4">
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
        @if($viewType === 'list' && $wishlists->count())
        <flux:table :paginate="$wishlists">
            <flux:table.columns>
                <!--<flux:table.column class="w-[20px]"><flux:checkbox.all /></flux:table.column>-->
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'set_prefix'" :direction="$sortDirection" wire:click="sort('set_prefix')">Set</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'collector_number'" :direction="$sortDirection" wire:click="sort('collector_number')">Collector Number</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'rarity'" :direction="$sortDirection" wire:click="sort('rarity')">Rarity</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'is_foil'" :direction="$sortDirection" wire:click="sort('is_foil')">Foil</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'card_count'" :direction="$sortDirection" wire:click="sort('card_count')">Amount</flux:table.column>
                <flux:table.columns></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($wishlists as $wishlist)
                    <flux:table.row 
                        :key="$wishlist->id"
                    >
                        <!--<flux:table.cell class="w-[20px]"><flux:checkbox /></flux:table.cell>-->
                        <flux:table.cell class="flex items-center gap-3 font-bold">
                            <flux:modal.trigger name="image-detail{{$wishlist->edition->id}}">
                                <flux:avatar size="xs" src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $wishlist->edition->slug }}.jpg" />
                            </flux:modal.trigger>
                            <a href="{{ route('card.details', ['cardId' => $wishlist->edition->card_id]) }}?edition={{ $wishlist->edition->set->prefix }}">{{ $wishlist->edition->card->name }}</a>
                            <flux:modal name="image-detail{{$wishlist->edition->id}}" class="md:w-96 space-y-6">
                                <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $wishlist->edition->slug }}.jpg">
                            </flux:modal>
                        </flux:table.cell>

                        <flux:table.cell>{{ $wishlist->edition->set->prefix ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>{{ $wishlist->edition->collector_number ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell><x-card-rarity :rarity="$wishlist->edition->rarity" /></flux:table.cell>
                        <flux:table.cell><x-card-foil :is_foil="$wishlist->is_foil" /></flux:table.cell>
                        <flux:table.cell>{{ $wishlist->card_count ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    @if($wishlist->user_id === auth()->id())
                                        <flux:modal.trigger name="remove-{{ $wishlist->id }}-delete">
                                            <flux:menu.item variant="danger" icon="trash">Remove</flux:menu.item>
                                        </flux:modal.trigger>
                                    @else
                                        <!-- Options for other users -->
                                        <flux:modal.trigger name="add-to-cart-{{ $wishlist->id }}">
                                            <flux:menu.item icon="shopping-cart">Add to cart</flux:menu.item>
                                        </flux:modal.trigger>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                            @if($wishlist->user_id === auth()->id())
                            <flux:modal name="remove-{{ $wishlist->id }}-delete" class="md:w-96 space-y-6">
                                <div>
                                    <flux:heading size="xl">wishlist (Amount: {{ $wishlist->card_count ?? 'N/A' }}) will be deleted</flux:heading>
                                </div>
                                <flux:spacer />
                                <div class="flex justify-center gap-1">
                                    <flux:modal.close>
                                        <flux:button variant="ghost">Cancel</flux:button>
                                    </flux:modal.close>

                                    <flux:button type="submit" variant="danger" wire:click="removeWishlist({{ $wishlist->id }})">Delete</flux:button>
                                </div>
                            </flux:modal>
                            @else
                            <flux:modal name="add-to-cart-{{ $wishlist->id }}" class="md:w-96 space-y-6">
                                <div>
                                    <flux:heading size="lg">Add to cart</flux:heading>
                                </div>

                                <flux:input.group>
                                    <flux:input.group.prefix>{{ $wishlist->card_count }}</flux:input.group.prefix>

                                    <flux:input name="amount" type="number" placeholder="Amount" wire:model="amount" min="1" max="{{ $wishlist->card_count }}"/>
                                </flux:input.group>

                                <div class="flex">
                                    <flux:spacer />

                                    <flux:button type="submit" variant="primary" wire:click="addToCart({{ $wishlist->id }})">Add</flux:button>
                                </div>
                            </flux:modal>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
        @elseif($viewType === 'box')
        <flux:table :paginate="$wishlists">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 my-7">
                @foreach($wishlists as $wishlist)
                    <flux:card class="p-4! pt-2! flex flex-col items-center">
                        <flux:heading size="lg" class="mb-2">{{ $wishlist->edition->card->name }}</flux:heading>
                        <div class="overflow-hidden rounded-xl flex">
                            <flux:modal.trigger name="image-detail-{{$wishlist->edition->id}}">
                                <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $wishlist->edition->slug }}.jpg" class="w-full"/>
                            </flux:modal.trigger>
                            <flux:modal name="image-detail-{{$wishlist->edition->id}}" class="md:w-96 space-y-6">
                                <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $wishlist->edition->slug }}.jpg">
                            </flux:modal>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <div class="flex flex-wrap gap-1">
                                <flux:heading>{{ $wishlist->edition->set->prefix ?? 'N/A' }}</flux:heading>
                                <flux:heading>-</flux:heading>
                                <flux:heading>{{ $wishlist->edition->collector_number ?? 'N/A' }}</flux:heading>
                            </div>
                            <flux:separator vertical />
                            <flux:heading><x-card-rarity :rarity="$wishlist->edition->rarity" /></flux:heading>
                        </div>
                        <div class="w-full! mt-2">
                            <div class="mt-2">
                                <flux:button.group class="w-full">
                                    <flux:button class="pointer-events-none w-full">
                                        <flux:heading>Count</flux:heading>
                                        <flux:separator vertical class="my-1" />
                                        <flux:heading>{{ $wishlist->card_count ?? 'N/A' }}</flux:heading>
                                    </flux:button>
                                    <flux:dropdown class="w-full">
                                        <flux:button variant="primary" icon="ellipsis-horizontal" class="w-full"></flux:button>

                                        <flux:menu>
                                            @if($wishlist->user_id === auth()->id())
                                                <flux:modal.trigger name="remove-{{ $wishlist->id }}-delete">
                                                    <flux:menu.item variant="danger" icon="trash">Remove</flux:menu.item>
                                                </flux:modal.trigger>
                                            @endif
                                        </flux:menu>
                                    </flux:dropdown>
                                </flux:button.group>
                            </div>
                        </div>
                        @if($wishlist->user_id === auth()->id())
                        <flux:modal name="remove-{{ $wishlist->id }}-delete" class="md:w-96 space-y-6">
                            <div>
                                <flux:heading size="xl">wishlist (Amount: {{ $wishlist->card_count ?? 'N/A' }}) will be deleted</flux:heading>
                            </div>
                            <flux:spacer />
                            <div class="flex justify-center gap-1">
                                <flux:modal.close>
                                    <flux:button variant="ghost">Cancel</flux:button>
                                </flux:modal.close>

                                <flux:button type="submit" variant="danger" wire:click="removeWishlist({{ $wishlist->id }})">Delete</flux:button>
                            </div>
                        </flux:modal>
                        @endif
                    </flux:card>
                @endforeach
            </div>
        </flux:table>
        @else
        <flux:heading class="mt-10">No results :(</flux:heading>
        @endif
    </flux:checkbox.group>
</div>
