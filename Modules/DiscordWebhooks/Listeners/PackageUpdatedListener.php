<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\PackageTemplate;

class PackageUpdatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = ':title Updated';
    public static string $description = ':name has been updated';
    public static string $color = '00ff00';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:package_updated', $e->getMessage());
        }
    }

    private function sendWebHook($event): void
    {
        app(PackageTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
