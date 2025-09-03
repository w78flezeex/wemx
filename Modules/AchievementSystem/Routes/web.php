<?php

use Illuminate\Support\Facades\Route;
use Modules\AchievementSystem\Http\Controllers\AchievementController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('achievements')->name('achievements.')->group(function () {
        Route::get('/', [AchievementController::class, 'index'])->name('index');
        Route::get('/{achievement}', [AchievementController::class, 'show'])->name('show');
        Route::get('/user/{user}', [AchievementController::class, 'userAchievements'])->name('user');
    });
});
