<?php

namespace Modules\DiscordWebhooks\Entities\Templates;

use Modules\DiscordWebhooks\Entities\DiscordEmbed;

class PaymentTemplate extends BaseTemplate
{
    protected function prepareEmbed($event): DiscordEmbed
    {
        $user = $event->payment->user;
        $payment = $event->payment;

        return (new DiscordEmbed())
            ->title($this->title)
            ->url(route('payments.edit', $payment->id))
            ->description($this->description)
            ->color($this->color)
            ->footer($payment->created_at, settings('logo', 'https://dev2.wemx.net/static/wemx.png'))
            ->thumbnail($user->avatar())
            ->author($user->username, route('users.edit', $user->id), $user->avatar())
            ->field('Payment ID', $payment->id, true)
            ->field('Payment Status', $payment->status, true);
    }
}

