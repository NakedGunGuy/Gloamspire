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
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </flux:card>

    <flux:table :paginate="$users">
        <flux:table.columns>
            <!--<flux:table.column class="w-[20px]"><flux:checkbox.all /></flux:table.column>-->
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'country'" :direction="$sortDirection" wire:click="sort('country')">Country</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell class="flex items-center gap-3 font-bold"><flux:avatar size="xs" src="{{ $user->avatar }}" /><a href="{{ route('user.details', $user->id) }}">{{ $user->name }}</a></flux:table.cell>
                    <flux:table.cell>
                        <x-country-flag :country-code="$user->country" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
