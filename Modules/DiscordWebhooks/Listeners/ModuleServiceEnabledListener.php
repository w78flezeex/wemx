<?php

namespace Modules\DiscordWebhooks\Listeners;

use Modules\DiscordWebhooks\Entities\Templates\ModuleTemplate;

class ModuleServiceEnabledListener
{
    public static string $title = ':title Enabled';
    public static string $description = ':name has been enabled';
    public static string $color = '00ff00';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:module_service_enabled', $e->getMessage());
        }
    }

    private function sendWebHook($event): void
    {
        $title = str_contains($event->module->getPath(), 'Modules') ? 'Module' : 'Service';
        app(ModuleTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
