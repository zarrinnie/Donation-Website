<?php

use App\Http\Controllers\DokuWebhookController;
use Illuminate\Support\Facades\Route;

// Server-to-server DOKU payment notification. Lives in the api group (no
// session, no CSRF); authenticity is enforced via HMAC signature verification
// inside DokuWebhookController itself.
Route::post('/webhooks/doku/notification', DokuWebhookController::class)
    ->name('webhooks.doku.notification');
