<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\OrderTemplate;

class OrderTerminatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = 'Order Terminated';
    public static string $description = 'An order has been terminated';
    public static string $color = 'ff0000';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:order_terminated', $e->getMessage());
        }

    }

    private function sendWebHook($event): void
    {
        app(OrderTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
