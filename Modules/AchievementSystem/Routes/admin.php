<?php

use Illuminate\Support\Facades\Route;
use Modules\AchievementSystem\Http\Controllers\Admin\AchievementController as AdminAchievementController;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('achievements')->name('achievements.')->group(function () {
        Route::get('/', [AdminAchievementController::class, 'index'])->name('index');
        Route::get('/create', [AdminAchievementController::class, 'create'])->name('create');
        Route::post('/', [AdminAchievementController::class, 'store'])->name('store');
        Route::get('/{achievement}/edit', [AdminAchievementController::class, 'edit'])->name('edit');
        Route::put('/{achievement}', [AdminAchievementController::class, 'update'])->name('update');
        Route::delete('/{achievement}', [AdminAchievementController::class, 'destroy'])->name('destroy');
    });
});
