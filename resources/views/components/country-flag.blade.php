@if ($country)
    <flux:tooltip content="{{ $country['name'] ?? ''}}" class="flex">
        <div>{{ $country['emoji'] ?? N/A }}</div>
    </flux:tooltip>
@endif