<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\PaymentTemplate;

class PaymentRefundedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = 'Payment Refunded';
    public static string $description = 'A payment has been refunded';
    public static string $color = 'ff0000';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:payment_refunded', $e->getMessage());
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
