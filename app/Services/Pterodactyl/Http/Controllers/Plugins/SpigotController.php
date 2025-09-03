<?php

namespace App\Services\Pterodactyl\Http\Controllers\Plugins;

use App\Facades\Theme;
use App\Models\Order;
use App\Services\Pterodactyl\Entities\Api\PluginModsHelper;
use App\Services\Pterodactyl\Entities\Api\Spigot;
use App\Services\Pterodactyl\Http\Controllers\OrderServer;
use Illuminate\Support\Facades\Http;

class SpigotController extends PluginController
{
    public function spigot(Order $order, $id = 'all')
    {
        $server = ptero()::server($order->id);
        OrderServer::checkPermission($order->id, $server['identifier']);
        $page = request()->get('page', 1);
        $search = request()->get('search', '');

        $spigot = new Spigot();
        if (!empty($search)) {
            $data = $spigot->searchResources($search, page: $page);
        } elseif ($id == 'all') {
            $data = $spigot->getPlugins(page: $page);
        } else {
            $data = $spigot->getResourcesByCategory($id, page: $page);
        }

        if (array_key_exists('error', $data) and $data['error']) {
            return redirect()->back();
        }

        $pagination = $data['pagination'];
        $plugins = collect($data['data']);
        $categories = collect($spigot->getCategories()['data']);

        return view(Theme::serviceView('pterodactyl', 'plugins.spigot'),
            compact('plugins', 'categories', 'order', 'server', 'pagination')
        );
    }

    public function installSpigot(Order $order, $resource)
    {
        $server = ptero()::server($order->id);
        OrderServer::checkPermission($order->id, $server['identifier']);
        ini_set('memory_limit', '200M');

        $spigot = new Spigot();
        $resourceDetails = $spigot->getDownloadInfo($resource);
        if (array_key_exists('premium', $resourceDetails) and $resourceDetails['premium']) {
            return redirect()->back()->with('error', 'This plugin is premium and cannot be installed.');
        }
        if ($this->isDirectFileUrl($resourceDetails['downloadUrl'])) {
            $fileContentResponse = Http::get($resourceDetails['downloadUrl']);
            PluginModsHelper::savePlugin($server['identifier'], $resourceDetails['name'], $fileContentResponse->body());
        } else {
            return redirect()->to($resourceDetails['downloadUrl']);
        }
        return redirect()->back()->with('success', 'Plugin installed successfully.');
    }
}
