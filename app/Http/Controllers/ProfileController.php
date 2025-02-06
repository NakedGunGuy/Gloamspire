<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Flux\Flux;

class ProfileController extends Controller
{

    public function discordAuth()
    {
        return Socialite::driver('discord')->redirect();
    }

    public function discordAuthCallback()
    {
        try {
            $discordUser = Socialite::driver('discord')->user();

            $user = User::where('discord_id', $discordUser->id)
                ->orWhere('email', $discordUser->email)
                ->first();

            if ($user) {
                $user->update([
                    'discord_id' => $discordUser->id,
                    'name' => $discordUser->name ?? $discordUser->nickname ?? '',
                    'avatar' => $discordUser->avatar,
                ]);
            } else {
                $user = User::create([
                    'discord_id' => $discordUser->id,
                    'email' => $discordUser->email,
                    'name' => $discordUser->name ?? $discordUser->nickname ?? '',
                    'avatar' => $discordUser->avatar,
                ]);
            }

            Auth::login($user);
       
            return redirect()->intended('/');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect()->route('home');
        } catch (\Exception $e) {
            Log::error('Discord OAuth callback error: ' . $e->getMessage());
            return redirect()->route('home');
        }
    }

}
