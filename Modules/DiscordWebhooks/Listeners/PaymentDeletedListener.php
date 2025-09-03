<?php

namespace Modules\DiscordWebhooks\Listeners;

use Modules\DiscordWebhooks\Entities\Templates\PaymentTemplate;

class PaymentDeletedListener
{

    public static string $title = 'Payment Deleted';
    public static string $description = 'A payment has been deleted';
    public static string $color = 'ff0000';

    public function handle($event): void
    {
        try {
            $this->sendWebHook($event);
        } catch (\Exception $e) {
            ErrorLog('discordwebhook:payment_deleted', $e->getMessage());
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
