<?php

namespace Modules\DiscordWebhooks\Listeners;

use Modules\DiscordWebhooks\Entities\Templates\ModuleTemplate;

class ModuleServiceDeletedListener
{

    public static string $title = ':title Deleted';
    public static string $description = ':name has been deleted';
    public static string $color = 'ff0000';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:module_service_deleted', $e->getMessage());
        }
    }

    private function sendWebHook($event): void
    {
        app(ModuleTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
