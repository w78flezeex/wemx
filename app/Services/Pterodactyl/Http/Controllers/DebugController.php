<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\AdminTheme;
use App\Http\Controllers\Controller;
use App\Services\Pterodactyl\Api\Pterodactyl;
use App\Services\Pterodactyl\Entities\Node;
use Exception;
use Illuminate\Support\Facades\Http;

class DebugController extends Controller
{
    public function debug()
    {
        config(['app.debug' => true]);
        $data = [];
        $old = ['method' => ''];
        $methods = ['servers', 'users', 'node', 'locations', 'nests', 'eggs'];
        if (request()->exists('method')) {
            $old['method'] = request()->get('method');
            try {
                $api = ptero()->api('application');
                $method = $old['method'];

                if ($method == 'eggs') {
                    $nests = $api->nests->all();
                    foreach ($nests['data'] as $nest) {
                        $egg = $api->eggs->all($nest['attributes']['id']);
                        if (isset($egg['data'])) {
                            $data[] = $egg['data'];
                        } else {
                            $data[] = $egg;
                        }
                    }
                } else {
                    $data = $api->$method->all()['data'];
                }
            } catch (\Exception $e) {
                $data = $e;
            }
        }
        $forceHttps = config('env.FORCE_HTTPS', 'false');
        $nodes = Node::all();
        $nodesIps = collect($nodes)->pluck('ip')->toArray();
        return view(AdminTheme::serviceView('pterodactyl', 'debug'),
            compact('nodesIps', 'forceHttps', 'nodes', 'data', 'old', 'methods')
        );
    }

    public function clearCache(){
        ptero()::clearCache();
        return redirect()->back()->with('success', __('responses.cleared_api_cache'));
    }

    public function checkOpenPort()
    {
        $port = request()->input('port');
        $host = request()->input('host');
        $error = null;
        $connection = @fsockopen($host, $port, $errno, $error, 2);
        if (is_resource($connection)) {
            fclose($connection);
            return response()->json(['success' => 'Port is error']);
        }
        return response()->json(['error' => $error]);
    }

    public function checkApiConnection()
    {
        $url = settings('encrypted::pterodactyl::api_url', '');
        $results = [
            'url_available' => false,
            'sso_authorized' => false,
            'client_api_available' => false,
        ];

        // Check URL accessibility
        try {
            $results['url_available'] = Http::head($url)->successful();
        } catch (Exception $e) {
            $results['url_available'] = false;
        }

        // SSO
        try {
            $results['sso_authorized'] = self::checkSsoAuthorization($url, settings('encrypted::pterodactyl::sso_secret', ''));
        } catch (Exception $e) {
            $results['sso_authorized'] = false;
        }
        // Client API
        try {
            $client = new Pterodactyl(settings('encrypted::pterodactyl::api_admin_key', ''), $url);
            $results['client_api_available'] = $client->checkAuthorizationClient();
        } catch (Exception $e) {
            $results['client_api_available'] = false;
        }
        return response()->json($results);
    }

    private static function checkSsoAuthorization($url, $secret): bool
    {
        $sso = Http::get($url . '/sso-wemx', [
            'sso_secret' => $secret,
            'user_id' => 1
        ]);

        if ($sso->successful() and is_array($sso->json())) {
            return true;
        } elseif ($sso->getStatusCode() == 501) {
            return $sso->json()['message'] == 'You cannot automatically login to admin accounts.';
        }
        return false;
    }
}
