<?php

namespace Modules\DiscordWebhooks\Entities\Templates;

use Modules\DiscordWebhooks\Entities\DiscordEmbed;

class ModuleTemplate extends BaseTemplate
{
    protected function prepareEmbed($event): DiscordEmbed
    {
        $module = $event->module;
        $title = str_contains($module->getPath(), 'Modules') ? 'Module' : 'Service';
        $user = $event->user;

        return (new DiscordEmbed())
            ->title(str_replace(':title', $title, $this->title))
            ->description(str_replace(':name', $module->getName(), $this->description))
            ->color($this->color)
            ->author($user->username, route('users.edit', $user->id), $user->avatar());
    }
}
