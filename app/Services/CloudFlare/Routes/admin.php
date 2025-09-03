<?php


use App\Services\CloudFlare\Http\Controllers\AdminController;


Route::get('/list', [AdminController::class, 'domainList'])->name('admin.cf.list');

Route::get('/pterodactyl', [AdminController::class, 'pterodactyl'])->name('admin.cf.pterodactyl');
Route::post('/pterodactyl', [AdminController::class, 'pteroStore'])->name('admin.cf.pterodactyl.store');
Route::post('/pterodactyl/{cfService}', [AdminController::class, 'pteroUpdate'])->name('admin.cf.pterodactyl.update');
Route::get('/pterodactyl/{cfService}/delete', [AdminController::class, 'pteroDestroy'])->name('admin.cf.pterodactyl.destroy');

Route::get('/wisp', [AdminController::class, 'wisp'])->name('admin.cf.wisp');
Route::post('/wisp', [AdminController::class, 'wispStore'])->name('admin.cf.wisp.store');
Route::post('/wisp/{cfService}', [AdminController::class, 'wispUpdate'])->name('admin.cf.wisp.update');
Route::get('/wisp/{cfService}/delete', [AdminController::class, 'wispDestroy'])->name('admin.cf.wisp.destroy');
