<?php

namespace Modules\DiscordWebhooks\Entities\Templates;

use Modules\DiscordWebhooks\Entities\DiscordEmbed;

class PackageTemplate extends BaseTemplate
{
    protected function prepareEmbed($event): DiscordEmbed
    {
        $package = $event->package;
        $icon_url = url('storage/products/' . $package->icon);
        return (new DiscordEmbed())
            ->title(str_replace(':title', $this->title, $package->name))
            ->url(route('packages.edit', $package->id))
            ->description(str_replace(':name', $package->name, $this->description))
            ->color($this->color)
            ->footer($package->created_at, settings('logo', 'https://dev2.wemx.net/static/wemx.png'))
            ->author($package->name, route('packages.edit', $package->id), $icon_url);
    }

}
