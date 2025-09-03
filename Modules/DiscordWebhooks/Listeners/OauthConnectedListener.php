<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\OauthTemplate;

class OauthConnectedListener implements ShouldQueue
{
    use InteractsWithQueue;
    public static string $title = 'Oauth';
    public static string $description = ':name has been connected to :driver';
    public static string $color = '00ff00';

    public function handle($event): void
    {
        dd($event);
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:oauth_connected', $e->getMessage());
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
