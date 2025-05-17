<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/clients', fn () => Inertia::render('clients/Index'))->name('clients');
    Route::get('/deals', fn () => Inertia::render('deals/Index'))->name('deals');
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
