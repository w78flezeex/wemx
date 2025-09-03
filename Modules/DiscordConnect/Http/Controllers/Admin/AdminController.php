<?php
namespace Modules\DiscordConnect\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Nwidart\Modules\Facades\Module;
use Illuminate\Http\Request;
use Modules\DiscordConnect\Entities\PackageEvent;
use Illuminate\Support\Facades\Http;
use Modules\DiscordConnect\Services\Discord;


class AdminController extends Controller
{
    public function index(Discord $discord) 
    {
        return view('discordconnect::admin.settings');
    }

    public function packages(Discord $discord) 
    {
        $events = PackageEvent::latest()->paginate(15);
        $roles = $discord->getRoles();

        return view('discordconnect::admin.packages', compact('events', 'roles'));
    }

    public function createEvent(Request $request) 
    {
        $request->validate([
            'name' => 'required',
            'event' => 'required',
            'all_packages' => 'boolean',
            'packages' => 'required_if:all_packages,0|array',
            'action' => 'required',
            'roles' => 'required|array',
        ]);

        $event = new PackageEvent();
        $event->name = $request->name;
        $event->event = $request->event;
        $event->action = $request->action;
        $event->roles = $request->roles ?? [];
        $event->packages = $request->packages ?? [];
        $event->all_packages = $request->all_packages ?? false;
        $event->save();

        return redirect()->route('admin.discord-connect.packages');
    }

    public function delete(PackageEvent $event) 
    {
        $event->delete();

        return redirect()->route('admin.discord-connect.packages');
    }
}
