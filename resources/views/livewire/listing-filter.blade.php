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
        @if($viewType === 'list' && $listings->count())
        <flux:table :paginate="$listings">
            <flux:table.columns>
                <!--<flux:table.column class="w-[20px]"><flux:checkbox.all /></flux:table.column>-->
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'set_prefix'" :direction="$sortDirection" wire:click="sort('set_prefix')">Set</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'collector_number'" :direction="$sortDirection" wire:click="sort('collector_number')">Collector Number</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'rarity'" :direction="$sortDirection" wire:click="sort('rarity')">Rarity</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'is_foil'" :direction="$sortDirection" wire:click="sort('is_foil')">Foil</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'user'" :direction="$sortDirection" wire:click="sort('user')">User</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'country'" :direction="$sortDirection" wire:click="sort('country')">Country</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'card_count'" :direction="$sortDirection" wire:click="sort('card_count')">Amount</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection" wire:click="sort('price')">Price</flux:table.column>
                <flux:table.columns></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($listings as $listing)
                    <flux:table.row 
                        :key="$listing->id"
                        class="{{ $listing->user_id === auth()->id() ? 'opacity-50' : '' }}"
                    >
                        <!--<flux:table.cell class="w-[20px]"><flux:checkbox /></flux:table.cell>-->
                        <flux:table.cell class="flex items-center gap-3 font-bold">
                            <flux:modal.trigger name="image-detail{{$listing->edition->id}}">
                                <flux:avatar size="xs" src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $listing->edition->slug }}.jpg" />
                            </flux:modal.trigger>
                            <a href="{{ route('card.details', ['cardId' => $listing->edition->card_id]) }}?edition={{ $listing->edition->set->prefix }}">{{ $listing->edition->card->name }}</a>
                            <flux:modal name="image-detail{{$listing->edition->id}}" class="md:w-96 space-y-6">
                                <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $listing->edition->slug }}.jpg">
                            </flux:modal>
                        </flux:table.cell>

                        <flux:table.cell>{{ $listing->edition->set->prefix ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>{{ $listing->edition->collector_number ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell><x-card-rarity :rarity="$listing->edition->rarity" /></flux:table.cell>
                        <flux:table.cell><x-card-foil :is_foil="$listing->is_foil" /></flux:table.cell>
                        <flux:table.cell>{{ $listing->user->name ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>
                            <x-country-flag :country-code="$listing->user->country" />
                        </flux:table.cell>
                        <flux:table.cell>{{ $listing->card_count ?? 'N/A' }}
                            @if($listing->user_id === auth()->id() && $listing->cart_count_sum)
                            <span class="opacity-50">+ {{ $listing->cart_count_sum}}</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $listing->price . ' €' ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    @if($listing->user_id === auth()->id())
                                        <flux:modal.trigger name="remove-listing-{{ $listing->id }}-delete">
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
                            <flux:modal name="remove-listing-{{ $listing->id }}-delete" class="md:w-96 space-y-6">
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
        @elseif($viewType === 'box')
        <flux:table :paginate="$listings">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 my-7">
                @foreach($listings as $listing)
                    <flux:card class="p-4! pt-2! flex flex-col items-center">
                        <flux:heading size="lg" class="mb-2">{{ $listing->edition->card->name }}</flux:heading>
                        <div class="overflow-hidden rounded-xl flex">
                            <flux:modal.trigger name="image-detail-{{$listing->edition->id}}">
                                <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $listing->edition->slug }}.jpg" class="w-full"/>
                            </flux:modal.trigger>
                            <flux:modal name="image-detail-{{$listing->edition->id}}" class="md:w-96 space-y-6">
                                <img src="https://ga-index-public.s3.us-west-2.amazonaws.com/cards/{{ $listing->edition->slug }}.jpg">
                            </flux:modal>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <div class="flex flex-wrap gap-1">
                                <flux:heading>{{ $listing->edition->set->prefix ?? 'N/A' }}</flux:heading>
                                <flux:heading>-</flux:heading>
                                <flux:heading>{{ $listing->edition->collector_number ?? 'N/A' }}</flux:heading>
                            </div>
                            <flux:separator vertical />
                            <flux:heading><x-card-rarity :rarity="$listing->edition->rarity" /></flux:heading>
                        </div>
                        <div class="w-full! mt-2">
                            <flux:card class="flex gap-2 p-2! justify-center">
                                <flux:heading>{{ $listing->user->name ?? 'N/A' }}</flux:heading>
                                <flux:separator vertical />
                                <x-country-flag :country-code="$listing->user->country" />
                            </flux:card>
                            <div class="mt-2">
                                <flux:button.group class="w-full">
                                    <flux:button class="pointer-events-none">
                                        <flux:heading>Count</flux:heading>
                                        <flux:separator vertical class="my-1" />
                                        <flux:heading>{{ $listing->card_count ?? 'N/A' }}</flux:heading>
                                        @if($listing->user_id === auth()->id() && $listing->cart_count_sum)
                                        <span class="opacity-50">+ {{ $listing->cart_count_sum}}</span>
                                        @endif
                                    </flux:button>
                                    <flux:button class="pointer-events-none">
                                        <flux:heading>Price</flux:heading>
                                        <flux:separator vertical class="my-1" />
                                        <flux:heading>{{ $listing->price . ' €' ?? 'N/A' }}</flux:heading>
                                    </flux:button>
                                    <flux:dropdown class="w-full">
                                        <flux:button variant="primary" icon="ellipsis-horizontal" class="w-full"></flux:button>

                                        <flux:menu>
                                            @if($listing->user_id === auth()->id())
                                                <flux:modal.trigger name="remove-listing-{{ $listing->id }}-delete">
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
                                </flux:button.group>
                            </div>
                        </div>
                        @if($listing->user_id === auth()->id())
                        <flux:modal name="remove-listing-{{ $listing->id }}-delete" class="md:w-96 space-y-6">
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
                    </flux:card>
                @endforeach
            </div>
        </flux:table>
        @else
        <flux:heading class="mt-10">No results :(</flux:heading>
        @endif
    </flux:checkbox.group>
</div>
