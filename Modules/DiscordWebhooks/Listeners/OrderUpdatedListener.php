<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\OrderTemplate;

class OrderUpdatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = 'Order Updated';
    public static string $description = 'An order has been updated';
    public static string $color = '0000ff';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:order_updated', $e->getMessage());
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
