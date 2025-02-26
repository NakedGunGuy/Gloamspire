<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Rinvex\Country\CountryLoader;

new class extends Component
{
    public string $name = '';
    public ?string $country = null;
    public array $countries = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = Auth::user()->name;
        $this->country = $user->country;

        // Load countries
        $this->countries = collect(CountryLoader::countries())->map(function ($country) {
            return [
                'name' => $country['name'], // Country name
                'iso2' => $country['iso_3166_1_alpha2'], // ISO Alpha-2 code (SI)
                'flag' => $country['emoji'], // Country emoji
            ];
        })->toArray();        
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'country' => ['nullable', 'string', 'max:255']
        ]);

        $user->fill($validated);

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);

        Flux::toast(
            text: 'User profile updated!',
            variant: 'success',
        );
    }
}; ?>

<section>
    <header>
        <flux:heading size="lg">
            {{ __('Profile Information') }}
        </flux:heading>

        <flux:subheading>
            {{ __("Update your account's profile information.") }}
        </flux:subheading>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <flux:select variant="listbox" searchable wire:model="country" id="country" name="country" label="{{__('Country')}}" placeholder="{{ __('Select your country') }}">
                @foreach ($countries as $country)
                    @if ($country == $this->country)
                    <flux:select.option value="{{ $country['iso2'] }}" selected>
                        {{ $country['flag'] }} {{ $country['name'] }}
                    </flux:select.option>
                    @else
                    <flux:select.option value="{{ $country['iso2'] }}">
                        {{ $country['flag'] }} {{ $country['name'] }}
                    </flux:select.option>
                    @endif
                @endforeach
            </flux:select>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
