<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\EventList;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::livewire('/events', 'event-list')->name('events.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
