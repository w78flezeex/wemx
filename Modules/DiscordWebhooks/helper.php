<?php

if (!function_exists('discordWebhook')) {
    function discordWebhook(): string
    {
        return Modules\DiscordWebhooks\Entities\WebhookManager::class;
    }
}
