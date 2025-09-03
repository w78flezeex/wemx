<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\Theme;
use App\Models\Order;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class NetworkController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            return OrderServer::handleOrderMiddleware($request, $next);
        });
    }

    public function network(Order $order)
    {
        $server = ptero()::server($order->id);
        OrderServer::savePermission($order->id, $server['identifier']);
        $allocations = ptero()->api("client")->network->all($server['identifier'])['data'];
        return view(Theme::serviceView('pterodactyl', 'network'), compact('order', 'server', 'allocations'));
    }

    public function assign(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $resp = ptero()->api("client")->network->assignAllocation($server);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.allocation_assigned_successfully'));
    }

    public function setNote(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'allocation' => 'required|string',
            'note' => 'required|string',
        ]);
        $resp = ptero()->api("client")->network->setNote($server, $data['allocation'], $data['note']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.note_set_successfully'));
    }

    public function setPrimary(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'allocation' => 'required|string',
        ]);
        $resp = ptero()->api("client")->network->setPrimary($server, $data['allocation']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        Cache::forget("server.ip.order.$order->id");
        return redirect()->back()->with('success', __('responses.allocation_primary_successfully'));
    }

    public function delete(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'allocation' => 'required|string',
        ]);
        $resp = ptero()->api("client")->network->delete($server, $data['allocation']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.allocation_delete_successfully'));
    }
}
