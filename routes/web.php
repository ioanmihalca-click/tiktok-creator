<?php

use App\Livewire\TikTokList;
use App\Livewire\CreateTikTok;
use Illuminate\Support\Facades\Route;

// Ruta publică pentru pagina de start
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Rutele protejate care necesită autentificare
Route::middleware(['auth'])->group(function () {
    // Dashboard-ul după autentificare
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profilul utilizatorului
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // Rutele pentru TikTok
    Route::get('/tiktoks', TikTokList::class)->name('tiktoks.index');
    Route::get('/tiktoks/create', CreateTikTok::class)->name('tiktoks.create');
    Route::get('/tiktoks/list', TikTokList::class)->name('tiktoks.list');
});

require __DIR__.'/auth.php';
