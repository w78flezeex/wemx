<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\ErrorTemplate;

class ErrorLogListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = 'Error Log';
    public static string $description = 'An error log has been created.';
    public static string $color = 'ff0000';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            // Log the error
        }
    }

    private function sendWebHook($event): void
    {
        app(ErrorTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
