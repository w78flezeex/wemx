<?php

namespace Modules\DiscordWebhooks\Entities\Templates;

use Modules\DiscordWebhooks\Entities\DiscordEmbed;

class ErrorTemplate extends BaseTemplate
{
    protected function prepareEmbed($event): DiscordEmbed
    {
        $title = $event->source ?? $this->title;

        return (new DiscordEmbed())
            ->title($title . ' - ' . strtoupper($event->severity))
            ->description($event->error ?? $this->description)
            ->color($this->color);
    }
}
