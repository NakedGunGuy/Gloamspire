@props(['is_foil'])


<flux:tooltip content="{{ $is_foil ? 'Foil' : 'Not Foil' }}">
    <flux:badge color="{{ $is_foil ? 'green' : 'red' }}" class="flex !px-1">
        @if($is_foil)
            <flux:icon.check variant="mini" />
        @else
            <flux:icon.x-mark variant="mini" />
        @endif
    </flux:badge>
</flux:tooltip>