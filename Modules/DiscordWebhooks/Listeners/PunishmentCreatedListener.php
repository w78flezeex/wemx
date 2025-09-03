<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\PunishmentTemplate;

class PunishmentCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = ':title';
    public static string $description = ':description';
    public static string $color = '00ff00';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:punishment_created', $e->getMessage());
        }
    }

    private function sendWebHook($event): void
    {
        app(PunishmentTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
