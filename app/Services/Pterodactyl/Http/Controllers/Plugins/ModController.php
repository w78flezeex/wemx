<?php

namespace App\Services\Pterodactyl\Http\Controllers\Plugins;

use App\Facades\Theme;
use App\Models\Order;
use App\Services\Pterodactyl\Entities\Api\PluginModsHelper;
use App\Services\Pterodactyl\Http\Controllers\FilesController;
use App\Services\Pterodactyl\Http\Controllers\OrderServer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ModController extends FilesController
{
    public function mods(Order $order)
    {
        $server = ptero()::server($order->id);
        OrderServer::savePermission($order->id, $server['identifier']);

        $installed = $this->getInstalledMods($server['identifier']);
        return view(Theme::serviceView('pterodactyl', 'mods.index'),
            compact('order', 'server', 'installed')
        );
    }

    public function toggle(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'name' => 'required|string',
        ]);
        PluginModsHelper::toggleMod($server, $data['name']);
        return redirect()->back()->with('success');
    }

    public function deleteMod(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'name' => 'required|string',
        ]);
        PluginModsHelper::deleteMod($server, $data['name']);
        return redirect()->back()->with('success');
    }

    protected function isDirectFileUrl(string $url): bool
    {
        try {
            $fileExtensions = ['.jar', '.zip', '.rar', '.tar', '.gz'];
            $path = parse_url($url, PHP_URL_PATH);
            foreach ($fileExtensions as $extension) {
                if (Str::endsWith($path, $extension)) {
                    return true;
                }
            }
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
            ])->head($url);
            if ($response->successful()) {
                $contentType = $response->header('Content-Type');
                if (Str::startsWith($contentType, 'application/') || Str::startsWith($contentType, 'text/plain')) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getInstalledMods($serverId)
    {
        $data = $this->filesPrepare(ptero()->api("client")->files->listFiles($serverId, '/mods'));
        return collect($data)->where('is_file', true)->values()->all();
    }

}
