<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\ExternalAccountTemplate;

class ExternalAccountCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = ':title Created';
    public static string $description = ':name has been created';
    public static string $color = '00ff00';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:external_account_create', $e->getMessage());
        }
    }

    private function sendWebHook($event): void
    {
        app(ExternalAccountTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
