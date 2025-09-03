<?php

namespace Modules\DiscordWebhooks\Entities\Templates;

use Modules\DiscordWebhooks\Entities\DiscordEmbed;

class OauthTemplate extends BaseTemplate
{
    protected function prepareEmbed($event): DiscordEmbed
    {
        $userOauth = $event->userOauth;
        $user = $userOauth->user;

        return (new DiscordEmbed())
            ->title($this->title)
            ->url(route('users.edit', $user->id))
            ->description(str_replace([':name', ':driver'], [$user->username, $userOauth->driver], $this->description))
            ->color($this->color)
            ->footer($user->created_at, settings('logo', 'https://dev2.wemx.net/static/wemx.png'))
            ->thumbnail($user->avatar())
            ->author($user->username, route('users.edit', $user->id), $user->avatar())
            ->field('User', $user->username, true)
            ->field('Driver', $userOauth->driver, true);
    }

}
