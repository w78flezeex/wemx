<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\Theme;
use App\Models\Order;
use Illuminate\Routing\Controller;

class FilesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            return OrderServer::handleOrderMiddleware($request, $next);
        });
    }

    public function files(Order $order)
    {
        $server = ptero()::server($order->id);
        OrderServer::savePermission($order->id, $server['identifier']);
        return view(Theme::serviceView('pterodactyl', 'files'), compact('order', 'server'));
    }

    public function all(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'path' => 'required|string',
        ]);
        $files = $this->filesPrepare(ptero()->api("client")->files->listFiles($server, $data['path']));
        $files = collect($files)->sortBy('is_file')->values()->all();
        return response()->json($files);
    }

    public function download(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'path' => 'required|string',
        ]);
        $file = ptero()->api("client")->files->downloadFile($server, $data['path']);
        return response()->json($file['attributes']);
    }

    public function createDirectory(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'name' => 'required|string',
            'path' => 'required|string',
        ]);
        ptero()->api("client")->files->createFolder($server, $data['name'], $data['path']);
    }

    public function rename(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'old_name' => 'required|string',
            'new_name' => 'required|string',
            'path' => 'required|string',
        ]);
        ptero()->api("client")->files->renameFile($server, $data['old_name'], $data['new_name'], $data['path']);
    }

    public function copy(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'path' => 'required|string',
        ]);
        ptero()->api("client")->files->copyFile($server, $data['path']);
    }

    public function delete(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'files' => 'required|array',
            'path' => 'required|string',
        ]);
        ptero()->api("client")->files->deleteFiles($server, $data['files'], $data['path']);
    }

    public function compress(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'files' => 'required|array',
            'path' => 'required|string',
        ]);
        ptero()->api("client")->files->compressFiles($server, $data['files'], $data['path']);
    }

    public function decompress(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'file_name' => 'required|string',
            'file_path' => 'required|string',
        ]);
        ptero()->api("client")->files->decompressFile($server, $data['file_name'], $data['file_path']);
    }

    public function write(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'file_path' => 'required|string',
            'content' => 'required|string',
        ]);
        ptero()->api("client")->files->writeFile($server, $data['file_path'], $data['content']);
    }

    public function getUploadUrl(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $url = ptero()->api("client")->files->getUploadUrl($server)['attributes']['url'];
        return response()->json(['url' => $url]);
    }

    public function getContent(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'file_path' => 'required|string',
        ]);
        $content = ptero()->api("client")->files->getFileContents($server, $data['file_path']);
        if (is_array($content) and isset($content['error']) and isset($content['response']) !== null) {
            return response()->make($content, 400, ['Content-Type' => 'text/plain']);
        }
        return response()->make($content, 200, ['Content-Type' => 'text/plain']);
    }

    // Private
    protected function filesPrepare($files): array
    {
        if (!isset($files['data']) || !is_array($files['data'])) {
            return [];
        }

        return array_map(function ($file) {
            $file['attributes']['size'] = bytesToHuman($file['attributes']['size']);
            $file['attributes']['modified_at'] = now()->parse($file['attributes']['modified_at'])->diffForHumans();
            return $file['attributes'];
        }, $files['data']);
    }


}
