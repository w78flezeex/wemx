<?php

use Illuminate\Support\Facades\Route;
use Modules\DiscordConnect\Http\Controllers\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('/admin/discord-connect')->middleware('permission')->group(function () {
    Route::get('/', [Admin\AdminController::class, 'index'])->name('admin.discord-connect.index');
    Route::get('/packages', [Admin\AdminController::class, 'packages'])->name('admin.discord-connect.packages');
    Route::post('/packages/create', [Admin\AdminController::class, 'createEvent'])->name('admin.discord-connect.packages.create');
    Route::get('/packages/{event}', [Admin\AdminController::class, 'delete'])->name('admin.discord-connect.packages.destroy');
});