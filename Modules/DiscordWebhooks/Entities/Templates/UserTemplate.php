<?php

namespace Modules\DiscordWebhooks\Entities\Templates;

use Modules\DiscordWebhooks\Entities\DiscordEmbed;

class UserTemplate extends BaseTemplate
{
    protected function prepareEmbed($event): DiscordEmbed
    {
        $user = $event->user;

        return (new DiscordEmbed())
            ->title($this->title)
            ->url(route('users.edit', $user->id))
            ->description($this->description)
            ->color($this->color)
            ->footer($user->created_at, settings('logo', 'https://dev2.wemx.net/static/wemx.png'))
            ->thumbnail($user->avatar())
            ->author($user->username, route('users.edit', $user->id), $user->avatar())
            ->field('User ID', $user->id, true);
    }
}

