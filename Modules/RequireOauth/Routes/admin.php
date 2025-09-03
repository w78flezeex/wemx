<?php

use Modules\RequireOauth\Http\Controllers\RequireOauthController;

Route::prefix('requireoauth')->name('requireoauth.')->group(function () {
    Route::get('/', [RequireOauthController::class, 'index'])->name('index');
    Route::post('/', [RequireOauthController::class, 'store'])->name('store');
})->middleware(['permission']);
