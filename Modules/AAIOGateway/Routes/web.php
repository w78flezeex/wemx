<?php

use Illuminate\Support\Facades\Route;
use Modules\AAIOGateway\Http\Controllers\CallbackController;

// Маршрут для callback'а от AAIO
// ВАЖНО: метод POST, как указано в настройках AAIO
Route::post('/callback', [CallbackController::class, 'handle'])->name('aaio.callback');