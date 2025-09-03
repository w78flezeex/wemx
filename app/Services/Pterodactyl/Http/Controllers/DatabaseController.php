<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\Theme;
use App\Models\Order;
use Illuminate\Routing\Controller;

class DatabaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            return OrderServer::handleOrderMiddleware($request, $next);
        });
    }
    public function databases(Order $order)
    {
        $server = ptero()::server($order->id);
        OrderServer::savePermission($order->id, $server['identifier']);
        $databases = ptero()->api("client")->database->get($server['identifier'])['data'] ?? [];
        return view(Theme::serviceView('pterodactyl', 'databases'), compact('order', 'server', 'databases'));
    }

    public function create(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'database' => 'required|string',
            'remote' => 'required|string',
        ]);
        $resp = ptero()->api("client")->database->create($server, $data['database'], $data['remote']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.database_create_successfully'));
    }

    public function delete(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'database' => 'required|string',
        ]);
        $resp = ptero()->api("client")->database->delete($server, $data['database']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.database_delete_successfully'));
    }

    public function resetPassword(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'database' => 'required|string',
        ]);
        $resp = ptero()->api("client")->database->resetPassword($server, $data['database']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.database_reset_pass_successfully'));
    }
}
