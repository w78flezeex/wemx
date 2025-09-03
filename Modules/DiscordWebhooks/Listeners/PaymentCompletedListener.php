<?php

namespace Modules\DiscordWebhooks\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\DiscordWebhooks\Entities\Templates\PaymentTemplate;

class PaymentCompletedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public static string $title = 'Payment Completed';
    public static string $description = 'A payment has been completed';
    public static string $color = '00ff00';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:payment_paid', $e->getMessage());
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
