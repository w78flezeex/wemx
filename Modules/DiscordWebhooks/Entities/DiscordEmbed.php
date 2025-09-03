<?php

namespace Modules\DiscordWebhooks\Entities;

class DiscordEmbed
{
    protected array $embed = [];

    public function title(string $title): self
    {
        $this->embed['title'] = $title;
        return $this;
    }

    public function description(string $description): self
    {
        $this->embed['description'] = $description;
        return $this;
    }

    public function url(string $url): self
    {
        $this->embed['url'] = $this->validateUrl($url);
        return $this;
    }

    public function color(int $color): self
    {
        $this->embed['color'] = ltrim($color, '#');
        return $this;
    }

    public function footer(string $text, string $icon_url = null): self
    {
        $this->embed['footer'] = ['text' => $text];
        if ($icon_url) {
            $this->embed['footer']['icon_url'] = $this->validateUrl($icon_url);
        }
        return $this;
    }

    public function thumbnail(string $url): self
    {
        $this->embed['thumbnail'] = ['url' => $this->validateUrl($url)];
        return $this;
    }

    public function image(string $url): self
    {
        $this->embed['image'] = ['url' => $this->validateUrl($url)];
        return $this;
    }

    public function author(string $name, string $url = null, string $icon_url = null): self
    {
        $this->embed['author'] = ['name' => $name];
        if ($url) {
            $this->embed['author']['url'] = $this->validateUrl($url);
        }
        if ($icon_url) {
            $this->embed['author']['icon_url'] = $this->validateUrl($icon_url);
        }
        return $this;
    }

    public function field(string $name, string $value, bool $inline = false): self
    {
        $this->embed['fields'][] = [
            'name' => $name,
            'value' => $value,
            'inline' => $inline
        ];
        return $this;
    }

    public function getEmbed(): array
    {
        return $this->embed;
    }

    private function validateUrl(string $url): string
    {
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : url($url);
    }
}
