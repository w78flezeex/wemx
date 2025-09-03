<?php

use Modules\DiscordWebhooks\Http\Controllers\DiscordWebhooksController;

Route::prefix('discord-webhooks')->group(function() {
    Route::get('/', [DiscordWebhooksController::class, 'index'])->name('admin.discord_webhooks');
    Route::get('/embed', [DiscordWebhooksController::class, 'embed'])->name('admin.discord_webhooks.embed');
    Route::post('/embed', [DiscordWebhooksController::class, 'embedSave'])->name('admin.discord_webhooks.embed_save');
    Route::get('/enable-all', [DiscordWebhooksController::class, 'enableAll'])->name('admin.discord_webhooks.enable_all');
    Route::get('/disable-all', [DiscordWebhooksController::class, 'disableAll'])->name('admin.discord_webhooks.disable_all');
});
