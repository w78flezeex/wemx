<?php

namespace App\Services\Pterodactyl\Http\Controllers\Plugins;

use App\Facades\Theme;
use App\Models\Order;
use App\Services\Pterodactyl\Entities\Api\Modrinth;
use App\Services\Pterodactyl\Entities\Api\PluginModsHelper;
use App\Services\Pterodactyl\Http\Controllers\OrderServer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ModrinthController extends ModController
{
    protected Modrinth $api;

    public function __construct()
    {
        $this->api = new Modrinth();
    }

    public function plugins(Order $order, string $id = 'all')
    {
        return $this->fetchResources($order, $id, 'plugin');
    }

    public function mods(Order $order, string $id = 'all')
    {
        return $this->fetchResources($order, $id, 'mod');
    }

    public function showPlugin(Order $order, string $project_id)
    {
        $server = ptero()::server($order->id);
        OrderServer::checkPermission($order->id, $server['identifier']);

        $resource = $this->api->getProject($project_id)['data'];
        if ($resource['error'] ?? false) {
            return back()->with('danger', $resource['message']);
        }
        $resource['versions'] = $this->api->getVersions($project_id)['data'];
        if ($resource['versions']['error'] ?? false) {
            return back()->with('danger', $resource['versions']['message']);
        }

        return view(Theme::serviceView('pterodactyl', 'plugins.modrinth.show'),
            compact('resource', 'order', 'server')
        );
    }

    public function showMod(Order $order, string $project_id)
    {
        $server = ptero()::server($order->id);
        OrderServer::checkPermission($order->id, $server['identifier']);

        $resource = $this->api->getProject($project_id)['data'];
        if ($resource['error'] ?? false) {
            return back()->with('danger', $resource['message']);
        }
        $resource['versions'] = $this->api->getVersions($project_id, type: 'mod')['data'];
        if ($resource['versions']['error'] ?? false) {
            return back()->with('danger', $resource['versions']['message']);
        }

        return view(Theme::serviceView('pterodactyl', 'mods.modrinth.show'),
            compact('resource', 'order', 'server')
        );
    }

    protected function fetchResources(Order $order, string $id, string $type)
    {
        $server = ptero()::server($order->id);
        OrderServer::checkPermission($order->id, $server['identifier']);

        $page = request()->get('page', 1);
        $search = request()->get('search', '');
        $categories = $this->api->getCategories();

        $data = match (true) {
            !empty($search) => $type === 'plugin'
                ? $this->api->getPlugins(page: $page, search: $search)
                : $this->api->getMods(page: $page, search: $search),
            $id === 'all' => $type === 'plugin'
                ? $this->api->getPlugins(page: $page)
                : $this->api->getMods(page: $page),
            default => $type === 'plugin'
                ? $this->api->getPlugins(page: $page, categories: [$id])
                : $this->api->getMods(page: $page, categories: [$id]),
        };
        $pagination = [
            'total_pages' => max(1, (int)ceil($data['data']['total_hits'] / $data['data']['limit'])),
            'current_page' => $page,
        ];

        $resources = collect($data['data']['hits']);
        return view(Theme::serviceView('pterodactyl', "{$type}s.modrinth"),
            compact('resources', 'categories', 'order', 'server', 'pagination')
        );
    }

    public function installModrinthPlugin(Order $order, string $project_id, string $version_id)
    {
        $server = ptero()::server($order->id);
        OrderServer::checkPermission($order->id, $server['identifier']);

        try {
            $resource = $this->api->getProject($project_id);
            if ($resource['error'] ?? false) {
                return back()->with('danger', $resource['message']);
            }

            $jar_name = PluginModsHelper::getJarName($resource['data']['slug']);
            $file = $this->api->getDownloadUrl($project_id, $version_id);
            if ($file['error'] ?? false) {
                return back()->with('danger', $file['message']);
            }

            $fileContentResponse = Http::get($file['url']);
            PluginModsHelper::savePlugin($server['identifier'], $jar_name, $fileContentResponse->body());
            return back()->with('success', 'Plugin installed successfully.');
        } catch (Exception $e) {
            Log::error("Failed to install Modrinth plugin: " . $e->getMessage());
            return back()->with('danger', 'An error occurred during plugin installation. Please try again.');
        }
    }

    public function installModrinthMod(Order $order, string $project_id, string $version_id)
    {
        $server = ptero()::server($order->id);
        OrderServer::checkPermission($order->id, $server['identifier']);

        try {
            $resource = $this->api->getProject($project_id);
            if ($resource['error'] ?? false) {
                return back()->with('danger', $resource['message']);
            }

            $jar_name = PluginModsHelper::getJarName($resource['data']['slug']);
            $file = $this->api->getDownloadUrl($project_id, $version_id);
            if ($file['error'] ?? false) {
                return back()->with('danger', $file['message']);
            }

            $fileContentResponse = Http::get($file['url']);
            PluginModsHelper::saveMod($server['identifier'], $jar_name, $fileContentResponse->body());
            return back()->with('success', 'Plugin installed successfully.');
        } catch (Exception $e) {
            Log::error("Failed to install Modrinth plugin: " . $e->getMessage());
            return back()->with('danger', 'An error occurred during plugin installation. Please try again.');
        }
    }
}
