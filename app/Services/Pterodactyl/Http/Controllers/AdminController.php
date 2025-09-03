<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\AdminTheme;
use App\Models\ErrorLog;
use App\Models\Order;
use App\Models\Package;
use App\Models\PackageSettings;
use App\Services\Pterodactyl\Entities\Node;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function nodes()
    {
        try {
            ptero()::clearCache();
            $nodes = Node::all();
        } catch (Exception $e) {
            $nodes = [];
            request()->session()->flash('error', $e->getMessage());
        }
        return view(AdminTheme::serviceView('pterodactyl', 'nodes'), compact('nodes'));
    }

    public function storeNode()
    {
        $data = request()->validate([
            'ports_range' => 'required',
            'location_id' => 'required',
            'node_id' => 'required',
            'ip' => 'required',
        ]);
        DB::table('pterodactyl_nodes')->updateOrInsert(['node_id' => $data['node_id']], $data);
        ptero()::clearCache();
        return redirect()->back()->with('success', __('admin.node_has_been_stored'));
    }

    public function packages()
    {
        $packages = Package::query()->where('service', 'pterodactyl')->get();
        return view(AdminTheme::serviceView('pterodactyl', 'packages'), compact('packages'));
    }

    public function storeRecommendCommands(Package $package)
    {
        $data = request()->validate([
            'commands' => 'nullable|string',
        ]);

        // Split the textarea input into an array of commands
        $commandsArray = array_filter(array_map('trim', explode("\n", $data['commands'])));

        PackageSettings::updateOrCreate(
            ['package_id' => $package->id, 'key' => 'commands'],
            ['value' => json_encode($commandsArray)]
        );
        return redirect()->back()->with('success');
    }

    public function wemxUsers()
    {
        $page = request()->input('page', 1);
        $perPage = 20;
        $users = ptero()->api()->users->all("?page=$page&per_page=$perPage&filter[external_id]=wmx-");
        $total = $users['meta']['pagination']['total'];
        $usersCollection = collect($users['data']);
        $users = new LengthAwarePaginator($usersCollection, $total, $perPage, $page,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
        return view(AdminTheme::serviceView('pterodactyl', 'users'), compact('users'));
    }

    public function wemxServers()
    {
        $page = request()->input('page', 1);
        $perPage = 20;
        $params = ['filter[external_id]' => 'wmx-', 'page' => $page, 'per_page' => $perPage];
        $servers = ptero()->api()->servers->all($params);
        $total = $servers['meta']['pagination']['total'];
        $usersCollection = collect($servers['data']);
        $servers = new LengthAwarePaginator($usersCollection, $total, $perPage, $page,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
        return view(AdminTheme::serviceView('pterodactyl', 'servers'), compact('servers'));
    }

    public function assignServerOrder()
    {
        $data = request()->validate([
            'server_uuid' => 'required',
            'order_id' => 'required|integer',
        ]);
        $order = Order::query()->find($data['order_id']);

        if (empty($order) or !empty($order->getExternalId())) {
            return redirect()->back()->with('error', __('admin.assign_order_error'));
        }
        $params = ['filter[uuidShort]' => $data['server_uuid']];
        $server = ptero()->api()->servers->all($params);
        if (is_array($server) and isset($server['data'][0])) {
            $server = $server['data'][0]['attributes'];
            if (!empty($server['external_id'])) {
                return redirect()->back()->with('error', __('admin.server_has_been_assigned_error'));
            }
            $params = ['name' => $server['name'], 'user' => $server['user'], 'external_id' => 'wmx-' . $data['order_id']];
            try {
                ptero()->api()->servers->update($server['id'], $params);
                $order->setExternalId($server['uuidShort']);
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
            return redirect()->back()->with('success', __('admin.server_has_been_assigned'));
        }
        return redirect()->back()->with('error', __('admin.server_not_found'));
    }

    public function logs()
    {
        $logs = ErrorLog::query()->latest()->where('source', 'like', 'pterodactyl%')->paginate(25);
        return view(AdminTheme::serviceView('pterodactyl', 'logs'), compact('logs'));
    }

    public function clearLogs()
    {
        ErrorLog::query()->where('source', 'like', 'pterodactyl%')->delete();
        return redirect()->back();
    }
}


