<?php

namespace Modules\DiscordWebhooks\Http\Controllers;

use App\Facades\AdminTheme;
use App\Models\Settings;
use Illuminate\Routing\Controller;
use Modules\DiscordWebhooks\Entities\WebhookManager;

class DiscordWebhooksController extends Controller
{
    public function index()
    {
        return view(AdminTheme::moduleView('discordwebhooks', 'index'));
    }

    public function embed()
    {
        $embedSettings = json_decode(Settings::get('discordwebhooks:embeds', '{}'), true);
        $events = WebhookManager::allEvents();
        return view(AdminTheme::moduleView('discordwebhooks', 'embed'), compact('events', 'embedSettings'));
    }

    public function embedSave()
    {
        $allSettings = json_decode(Settings::get('discordwebhooks:embeds', '{}'), true);
        $data = request()->except('_token');
        $data = array_merge($allSettings, $data);
        Settings::put('discordwebhooks:embeds', $data);
        return redirect()->back()->with('success', 'Embed settings saved successfully. Please restart the queue worker to apply the changes. Use the following command to restart the queue worker: <code>php artisan queue:start --force</code>');
    }

    public function enableAll()
    {
        $events = WebhookManager::allEvents();
        foreach ($events as $name => $event) {
            if (!empty($event['description'])){
                Settings::put('discordwebhook:' . $name, true);
            }

        }
        return redirect()->back()->with('success', 'All events enabled successfully. Please restart the queue worker to apply the changes. Use the following command to restart the queue worker: <code>php artisan queue:start --force</code>');
    }

    public function disableAll()
    {
        $events = WebhookManager::allEvents();
        foreach ($events as $name => $event) {
            Settings::put('discordwebhook:' . $name, false);
        }
        return redirect()->back()->with('success', 'All events disabled successfully. Please restart the queue worker to apply the changes. Use the following command to restart the queue worker: <code>php artisan queue:start --force</code>');
    }
}
