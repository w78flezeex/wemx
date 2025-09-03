<?php

namespace Modules\DiscordWebhooks\Listeners;

use Modules\DiscordWebhooks\Entities\Templates\OauthTemplate;

class OauthUpdatedListener
{
    public static string $title = 'Oauth';
    public static string $description = ':name has been updated to :driver';
    public static string $color = '00ff00';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:oauth_updated', $e->getMessage());
        }
    }

    private function sendWebHook($event): void
    {
        app(OauthTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
