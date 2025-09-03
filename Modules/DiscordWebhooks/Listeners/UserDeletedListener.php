<?php

namespace Modules\DiscordWebhooks\Listeners;

use Modules\DiscordWebhooks\Entities\Templates\UserTemplate;

class UserDeletedListener
{
    public static string $title = 'User Deleted';
    public static string $description = 'A user has been deleted';
    public static string $color = 'ff0000';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:user_deleted', $e->getMessage());
        }
    }

    private function sendWebHook($event): void
    {
        app(UserTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
