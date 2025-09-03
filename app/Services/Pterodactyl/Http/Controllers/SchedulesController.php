<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\Theme;
use App\Models\Order;
use Illuminate\Routing\Controller;

class SchedulesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            return OrderServer::handleOrderMiddleware($request, $next);
        });
    }

    public function schedules(Order $order)
    {
        $server = ptero()::server($order->id);
        OrderServer::savePermission($order->id, $server['identifier']);
        $schedules = ptero()->api("client")->schedules->all($server['identifier'])['data'];
        return view(Theme::serviceView('pterodactyl', 'schedules'), compact('order', 'server', 'schedules'));
    }

    public function get(Order $order, $server, $schedule)
    {
        OrderServer::checkPermission($order->id, $server);
        $schedule = ptero()->api("client")->schedules->get($server, $schedule)['attributes'];
        return view(Theme::serviceView('pterodactyl', 'schedule_view'), compact('order', 'server', 'schedule'));
    }

    public function create(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'name' => 'required|string',
            'minute' => 'required|string',
            'hour' => 'required|string',
            'month' => 'required|string',
            'day_of_week' => 'required|string',
            'day_of_month' => 'required|string',
            'is_active' => 'nullable|boolean',
            'only_when_online' => 'nullable|boolean',
        ]);
        $resp = ptero()->api("client")->schedules->create($server,
            $data['name'], $data['minute'], $data['hour'], $data['month'], $data['day_of_week'], $data['day_of_month'],
            $data['is_active'] ?? false, $data['only_when_online'] ?? true);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.schedule_create_successfully'));
    }

    public function update(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'schedule_id' => 'required|string',
            'name' => 'required|string',
            'minute' => 'required|string',
            'hour' => 'required|string',
            'month' => 'required|string',
            'day_of_week' => 'required|string',
            'day_of_month' => 'required|string',
            'is_active' => 'nullable|boolean',
            'only_when_online' => 'nullable|boolean',
        ]);
        $resp = ptero()->api("client")->schedules->update($server, $data['schedule_id'],
            $data['name'], $data['minute'], $data['month'], $data['hour'], $data['day_of_week'], $data['day_of_month'],
            $data['is_active'] ?? false, $data['only_when_online'] ?? true);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.schedule_update_successfully'));
    }

    public function execute(Order $order, $server, $schedule)
    {
        OrderServer::checkPermission($order->id, $server);
        $resp = ptero()->api("client")->schedules->execute($server, $schedule);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.schedule_execute_successfully'));
    }

    public function delete(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'schedule_id' => 'required|string',
        ]);
        $resp = ptero()->api("client")->schedules->delete($server, $data['schedule_id']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->route('pterodactyl.schedules', ['order' => $order->id])->with('success', __('responses.schedule_delete_successfully'));
    }

    public function createTask(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'schedule_id' => 'required|string',
            'task' => 'required|array',
        ]);
        $resp = ptero()->api("client")->schedules->createTask($server, $data['schedule_id'], $data['task']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.task_create_successfully'));
    }

    public function updateTask(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'schedule_id' => 'required|string',
            'task_id' => 'required|string',
            'task' => 'required|array',
        ]);
        $resp = ptero()->api("client")->schedules->updateTask($server, $data['schedule_id'], $data['task_id'], $data['task']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.task_update_successfully'));
    }

    public function deleteTask(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'schedule_id' => 'required|string',
            'task_id' => 'required|string',
        ]);
        $resp = ptero()->api("client")->schedules->deleteTask($server, $data['schedule_id'], $data['task_id']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.task_delete_successfully'));
    }
}
