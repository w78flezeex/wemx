<?php

namespace App\Services\Pterodactyl\Entities\Api;

use Illuminate\Support\Str;

class PluginModsHelper
{
    public static function getJarName($fullName, $version = null): string
    {
        if ($version) {
            return Str::slug(Str::limit($fullName, 20)) . '_' . $version . '.jar';
        }
        return Str::slug(Str::limit($fullName, 20)) . '.jar';
    }

    public static function togglePlugin($serverId, $name): void
    {
        if (str_contains($name, '.disabled')) {
            $newName = str_replace('.disabled', '', $name);
        } else {
            $newName = $name . '.disabled';
        }
        ptero()->api("client")->files->renameFile($serverId, $name, $newName, '/plugins');
    }

    public static function toggleMod($serverId, $name): void
    {
        if (str_contains($name, '.disabled')) {
            $newName = str_replace('.disabled', '', $name);
        } else {
            $newName = $name . '.disabled';
        }
        ptero()->api("client")->files->renameFile($serverId, $name, $newName, '/mods');
    }

    public static function savePlugin($serverId, $pluginName, $fileContent): void
    {
        if (!Str::contains($pluginName, '.jar')) {
            $pluginName = self::getJarName($pluginName);
        }
        ptero()->api("client")->files->writeFile($serverId, "/plugins/{$pluginName}", $fileContent);
    }

    public static function saveMod($serverId, $pluginName, $fileContent): void
    {
        if (!Str::contains($pluginName, '.jar')) {
            $pluginName = self::getJarName($pluginName);
        }
        ptero()->api("client")->files->writeFile($serverId, "/mods/{$pluginName}", $fileContent);
    }

    public static function deletePlugin($serverId, $pluginName): void
    {
        ptero()->api("client")->files->deleteFiles($serverId, [$pluginName], '/plugins');
    }

    public static function deleteMod($serverId, $pluginName): void
    {
        ptero()->api("client")->files->deleteFiles($serverId, [$pluginName], '/mods');
    }
}
