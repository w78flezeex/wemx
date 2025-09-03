<?php

use Illuminate\Support\Facades\Route;
use Modules\CryptoBotGateway\Http\Controllers\WebhookController;

// Маршрут для Webhook'а от CryptoBot
Route::post('/webhook', [WebhookController::class, 'handle'])->name('cryptobot.webhook');
