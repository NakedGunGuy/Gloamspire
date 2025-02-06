<div>
    <flux:card class="flex flex-wrap items-center justify-between gap-2 md:gap-4">
        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-4">
            <flux:input 
                icon="magnifying-glass" 
                placeholder="Search name" 
                type="text" 
                wire:model.live="search"
            />
        </div>
        <div class="flex flex-wrap justify-end gap-2 w-full md:w-fit md:gap-4">
            <flux:select 
                wire:model.live="perPage" 
                variant="listbox" 
                placeholder="Per Page" 
                class="min-w-[50px] max-w-[100px]"
            >
                @foreach($perPageOptions as $option)
                    <flux:option value="{{ $option }}">{{ $option }}</flux:option>
                @endforeach
            </flux:select>
        </div>
    </flux:card>

    <flux:table :paginate="$users">
        <flux:columns>
            <!--<flux:column class="w-[20px]"><flux:checkbox.all /></flux:column>-->
            <flux:column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:column>
            <flux:column sortable :sorted="$sortBy === 'country'" :direction="$sortDirection" wire:click="sort('country')">Country</flux:column>
        </flux:columns>

        <flux:rows>
            @foreach($users as $user)
                <flux:row :key="$user->id">
                    <flux:cell class="flex items-center gap-3 font-bold"><flux:avatar size="xs" src="{{ $user->avatar }}" /><a href="{{ route('user.details', $user->id) }}">{{ $user->name }}</a></flux:cell>
                    <flux:cell>
                        <x-country-flag :country-code="$user->country" />
                    </flux:cell>
                </flux:row>
            @endforeach
        </flux:rows>
    </flux:table>
</div>
