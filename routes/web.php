<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\OrderDetails;

Route::view('/', 'home')
    ->name('home');

Route::view('listings', 'listings.index')
    ->name('listings');

Route::view('cards', 'cards.index')
    ->name('cards');

Route::view('users', 'users.index')
    ->name('users');

Route::view('cart', 'cart.index')
    ->middleware(['auth'])
    ->name('cart');

Route::view('order', 'order.index')
    ->middleware(['auth'])
    ->name('order');

Route::get('/order/{order}', \App\Livewire\OrderDetails::class)->name('order.details');

Route::get('/user/{userId}', \App\Livewire\UserDetails::class)->name('user.details');

Route::get('/card/{cardId}', \App\Livewire\CardDetails::class)->name('card.details');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';