<?php

use App\Http\Controllers\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Midtrans webhook (no auth required)
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])
    ->name('midtrans.webhook');
