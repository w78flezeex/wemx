<?php

use App\Http\Controllers\Admin\ServicesController;
use App\Services\Pterodactyl\Http\Controllers\AdminController;
use App\Services\Pterodactyl\Http\Controllers\DebugController;

Route::get('/config', [ServicesController::class, 'config'])->defaults('service', 'pterodactyl')->name('pterodactyl.config')->middleware('permission');
Route::get('/nodes', [AdminController::class, 'nodes'])->name('pterodactyl.nodes')->middleware('permission');
Route::post('/nodes/store', [AdminController::class, 'storeNode'])->name('pterodactyl.nodes.store')->middleware('permission');
Route::get('/users', [AdminController::class, 'wemxUsers'])->name('pterodactyl.users')->middleware('permission');
Route::get('/servers', [AdminController::class, 'wemxServers'])->name('pterodactyl.servers')->middleware('permission');
Route::post('/servers', [AdminController::class, 'assignServerOrder'])->name('pterodactyl.server.assign')->middleware('permission');
Route::get('/logs', [AdminController::class, 'logs'])->name('pterodactyl.logs')->middleware('permission');
Route::get('/logs/clear', [AdminController::class, 'clearLogs'])->name('pterodactyl.logs.clear')->middleware('permission');

Route::get('/packages', [AdminController::class, 'packages'])->name('pterodactyl.packages')->middleware('permission');
Route::post('/packages/{package}/commands', [AdminController::class, 'storeRecommendCommands'])->name('pterodactyl.commands.store')->middleware('permission');


Route::get('/clear/cache', [DebugController::class, 'clearCache'])->name('pterodactyl.clear_cache');
Route::any('/debug', [DebugController::class, 'debug'])->name('pterodactyl.debug')->middleware('permission');
Route::get('/debug/port', [DebugController::class, 'checkOpenPort'])->name('pterodactyl.debug.port')->middleware('permission');
Route::get('/debug/api', [DebugController::class, 'checkApiConnection'])->name('pterodactyl.debug.api')->middleware('permission');
