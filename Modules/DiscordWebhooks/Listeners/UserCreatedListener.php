<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\UserTemplate;

class UserCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = 'User Created';
    public static string $description = 'A user has been created';
    public static string $color = '00ff00';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:user_created', $e->getMessage());
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
