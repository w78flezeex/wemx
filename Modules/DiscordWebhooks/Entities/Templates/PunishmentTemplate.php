<?php

namespace Modules\DiscordWebhooks\Entities\Templates;

use App\Events\ErrorLog;
use Illuminate\Support\Str;
use Modules\DiscordWebhooks\Entities\DiscordEmbed;

class PunishmentTemplate extends BaseTemplate
{
    protected function prepareEmbed($event): DiscordEmbed
    {
        $punishment = $event->punishment;
        $client = $punishment->user;
        $staff = $punishment->staff;
        $type = Str::upper($punishment->type);
        $client_name = Str::upper($client->username);
        if ($type == 'BAN') {
            $this->setColor('ff0000');
        } elseif ($type == 'IP BAN') {
            $this->setColor('ff0000');
        } elseif ($type == 'UNBAN') {
            $this->setColor('00ff00');
        } elseif ($type == 'WARNING') {
            $this->setColor('ffff00');
        }

        return (new DiscordEmbed())
            ->title(str_replace(':title', $type, $this->title . ' ' . $client_name))
            ->url(route('admin.user.punishments', $client->id))
            ->description(str_replace(':description', $punishment->reason, $this->description))
            ->color($this->color)
            ->footer($punishment->created_at->format('d.m.Y'), settings('logo', 'https://dev2.wemx.net/static/wemx.png'))
            ->thumbnail($client->avatar())
            ->author($staff->username, route('users.edit', $staff->id), $staff->avatar())
            ->field('User', $client_name, true)
            ->field('Expired', $punishment->expires_at ? $punishment->expires_at->diffForHumans() : 'Forever', true);
    }

}
