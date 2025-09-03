<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\PaymentTemplate;

class PaymentUpdatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = 'Payment Updated';
    public static string $description = 'A payment has been updated';
    public static string $color = 'ffff00';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:payment_updated', $e->getMessage());
        }
    }

    private function sendWebHook($event): void
    {
        app(PaymentTemplate::class)
            ->setColor(self::$color)
            ->setTitle(self::$title)
            ->setDescription(self::$description)
            ->send($event);
    }
}
