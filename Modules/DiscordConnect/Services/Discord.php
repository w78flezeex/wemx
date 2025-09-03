<?php

namespace Modules\DiscordConnect\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class Discord
{
    public function getRoles()
    {
        $roles = Cache::remember('discord-roles', 60, function() {
            $response = $this->api('roles');
            return $response->collect();
        });

        return $roles;
    }

    public function giveRoles($userId, array $roles)
    {
        foreach($roles as $roleId) {
            try {
                $this->api("members/{$userId}/roles/{$roleId}", [], 'PUT');
            } catch(\Exception $e) {
                // Log the error
                ErrorLog('Discord:Connect', 'Failed to give role to user', $e->getMessage());
            }
        }
    }

    public function removeRoles($userId, array $roles)
    {
        foreach($roles as $roleId) {
            try {
                $this->api("members/{$userId}/roles/{$roleId}", [], 'DELETE');
            } catch(\Exception $e) {
                // Log the error
                ErrorLog('Discord:Connect', 'Failed to remove role from user', $e->getMessage());
            }
        }
    }
    
    public function api($endpoint, $data = [], $method = 'GET')
    {
        $botToken = settings('encyroted::discord-connect::bot_token');
        $discordServer = settings('discord-connect::discord_server');

        if(!$botToken) {
            throw new \Exception("[Discord] Bot token is not set. Please set the bot token in the settings.");
        }

        if(!$discordServer) {
            throw new \Exception("[Discord] Discord server ID is not set. Please set the Discord server ID in the settings.");
        }

        $url = "https://discord.com/api/guilds/{$discordServer}/{$endpoint}";

        $response = Http::withHeaders([
            'Authorization' => "Bot {$botToken}",
        ])->$method($url, $data);

        if($response->failed()) {
            throw new \Exception("[Discord] Failed to connect to the Discord API. Ensure the bot token and Discord server ID are valid.");
        }

        return $response;
    }
}

