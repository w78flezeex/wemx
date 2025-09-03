<?php

namespace Modules\DiscordWebhooks\Entities\Templates;

use Modules\DiscordWebhooks\Entities\DiscordEmbed;
use Modules\DiscordWebhooks\Entities\WebhookManager;

abstract class BaseTemplate
{
    protected string $title;
    protected string $description;
    protected string $color;
    protected string $webhook_url;

    abstract protected function prepareEmbed($event): DiscordEmbed;

    public function send($event): void
    {
        $this->setWebhookUrl();
        $this->setSettings(class_basename($event));
        if (!$this->webhook_url) return;

        $embed = $this->prepareEmbed($event);

        $webhookManager = new WebhookManager($this->webhook_url);
        $webhookManager->appendEmbed($embed->getEmbed())
            ->appendUsername(settings('app_name', 'WemX'))
            ->appendAvatar(settings('logo', 'https://dev2.wemx.net/static/wemx.png'))
            ->appendTTS(false)
            ->send();
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setColor(string $color): self
    {
        $this->color = hexdec($color);
        return $this;
    }

    protected function setSettings(string $eventName): void
    {
        $settings = WebhookManager::getEmbedSettings($eventName) ?? [];
        if (array_key_exists('color', $settings)) {
            $this->setColor($settings['color']);
        }
        if (array_key_exists('title', $settings)) {
            $this->setTitle($settings['title']);
        }
        if (array_key_exists('description', $settings)) {
            $this->setDescription($settings['description']);
        }
        if (array_key_exists('webhook', $settings) && $settings['webhook'] !== '') {
            $this->webhook_url = $settings['webhook'];
        }
    }

    protected function setWebhookUrl(): void
    {
        $this->webhook_url = settings('discordwebhook:webhook');
    }
}

