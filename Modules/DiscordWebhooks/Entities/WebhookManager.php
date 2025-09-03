<?php

namespace Modules\DiscordWebhooks\Entities;

use App\Models\Settings;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class WebhookManager
{
    public string $webhook_url;
    protected array $payload = [];

    public function __construct(string $webhook_url)
    {
        $this->webhook_url = $webhook_url;
    }

    public function send(): ResponseInterface
    {
        $client = new Client();
        $response = $client->post($this->webhook_url, [
            'json' => $this->payload
        ]);
        $this->payload = [];
        return $response;
    }

    public function appendEmbed($embed): self
    {
        $this->payload['embeds'][] = $embed;
        return $this;
    }

    public function appendContent($content): self
    {
        $this->payload['content'] = $content;
        return $this;
    }

    public function appendUsername($username): self
    {
        $this->payload['username'] = $username;
        return $this;
    }

    public function appendAvatar($avatar): self
    {
        if (!filter_var($avatar, FILTER_VALIDATE_URL)) {
            $avatar = asset($avatar);
        }
        $this->payload['avatar_url'] = $avatar;
        return $this;
    }

    public function appendTTS($tts): self
    {
        $this->payload['tts'] = $tts;
        return $this;
    }

    public static function allEvents(): Collection
    {
        return collect(require base_path('Modules/DiscordWebhooks/Config/events.php'));

    }

    public static function allEnabledEvents(): Collection
    {
        return self::allEvents()->filter(fn($event, $name) => self::eventEnabled($name) ? $event : null);
    }

    public static function getBuyFilter($filter = 'order'): Collection
    {
        return discordWebhook()::allEvents()->filter(function ($value, $key) use ($filter) {
            return Str::contains($key, $filter);
        });
    }

    public static function eventEnabled($event, $default = false): bool
    {
        return settings('discordwebhook:' . $event, $default);
    }

    public static function getEmbedSettings($eventClassName): array
    {
        return Settings::getJson('discordwebhooks:embeds', $eventClassName, []);
    }
}
