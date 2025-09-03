<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\OrderTemplate;

class OrderActivatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = 'Order Activated';
    public static string $description = 'An order has been activated';
    public static string $color = '00ff00';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:order_activated', $e->getMessage());
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
