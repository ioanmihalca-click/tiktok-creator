<?php

use App\Livewire\TikTokList;
use App\Livewire\CreateTikTok;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\VideoDownloadController;

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
    Route::get('/tiktoks/create', CreateTikTok::class)->name('tiktoks.create');
    Route::get('/tiktoks/list', TikTokList::class)->name('tiktoks.list');

    // Rutele pentru credite
    Route::prefix('credits')->group(function () {
        Route::get('/', [CreditController::class, 'index'])->name('credits.index');
        Route::get('/checkout/{id}', [CreditController::class, 'checkout'])->name('credits.checkout');
        Route::get('/success', [CreditController::class, 'success'])->name('credits.success');
        Route::get('/cancel', [CreditController::class, 'cancel'])->name('credits.cancel');
        Route::get('/history', [CreditController::class, 'history'])->name('credits.history');
    });
});

// Webhook Stripe (nu necesită autentificare)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

require __DIR__ . '/auth.php';
