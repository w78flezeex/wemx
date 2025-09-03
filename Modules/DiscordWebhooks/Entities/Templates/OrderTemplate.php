<?php

namespace Modules\DiscordWebhooks\Entities\Templates;

use Modules\DiscordWebhooks\Entities\DiscordEmbed;

class OrderTemplate extends BaseTemplate
{
    protected function prepareEmbed($event): DiscordEmbed
    {
        $user = $event->order->user;
        $order = $event->order;

        return (new DiscordEmbed())
            ->title($this->title)
            ->url(route('orders.edit', $order->id))
            ->description($this->description)
            ->color($this->color)
            ->footer($order->created_at, settings('logo', 'https://dev2.wemx.net/static/wemx.png'))
            ->thumbnail(asset('storage/products/' . $order->package->icon))
            ->author($user->username, route('users.edit', $user->id), $user->avatar())
            ->field('Order ID', $order->id, true)
            ->field('Order Status', $order->status, true);
    }
}

