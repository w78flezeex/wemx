<?php

namespace App\Services\Pterodactyl;

use App\Models\Order;
use App\Models\Package;
use App\Services\Pterodactyl\Entities\Node;
use App\Services\Pterodactyl\Entities\Placeholder;
use App\Services\Pterodactyl\Entities\Server;
use App\Services\ServiceInterface;
use Exception;
use Illuminate\Support\Arr;

class Service implements ServiceInterface
{
    public static string $key = 'pterodactyl';
    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // Service Info & Config
    public static function metaData(): object
    {
        $config = require __DIR__ . '/Config/config.php';
        return (object)
        [
            'display_name' => $config['name'],
            'author' => $config['author'],
            'version' => $config['version'],
            'wemx_version' => $config['wemx_version'],
        ];
    }

    public static function setConfig(): array
    {
        return [
            [
                'key' => 'encrypted::pterodactyl::api_url',
                'name' => 'Api Url',
                'description' => 'Pterodactyl api url',
                'type' => 'text',
                'rules' => ['required', 'url']
            ],
            [
                'key' => 'encrypted::pterodactyl::api_admin_key',
                'name' => 'Admin private API key',
                'description' => 'Pterodactyl administrator api client key (Account > API credentials)',
                'type' => 'password',
                'rules' => ['required','starts_with:ptlc_']
            ],
            [
                'key' => 'encrypted::pterodactyl::sso_secret',
                'name' => 'Sso Key',
                'description' => 'Pterodactyl sso key',
                'type' => 'password',
                'rules' => ['nullable']
            ],
            [
                'key' => 'pterodactyl::short_location_name',
                'name' => 'Short location name',
                'description' => 'Show short location name',
                'type' => 'select',
                'options' => ['1' => 'Yes', '0' => 'No'],
                'default_value' => '0',
                'rules' => ['nullable']
            ],
            [
                'key' => 'pterodactyl::ip_button',
                'name' => 'Ip button',
                'description' => 'Show ip button',
                'type' => 'bool',
                'default_value' => true,
                'rules' => ['nullable']
            ],
            [
                'key' => 'pterodactyl::file_manager_double_click',
                'name' => 'File manager double click',
                'description' => 'Enable double click to open file or folder',
                'type' => 'bool',
                'default_value' => true,
                'rules' => ['nullable']
            ],

        ];
    }

    public static function setPackageConfig(Package $package): array
    {
        $egg_id = '';
        $location_ids = [];
        $variablesBtn = [];
        $serverBtn = [];
        $placeholders = Placeholder::PLACEHOLDERS;
        if (array_key_exists('egg', $package->data ?? [])) {
            $egg_id = is_numeric($package->data['egg']) ? $package->data['egg'] : json_decode($package->data['egg'], true)['id'];
            $variablesBtn = pteroHelper()::variablesToOptions($egg_id);
            $serverBtn = pteroHelper()::getServerParamsOptions($egg_id);
        }

        if (array_key_exists('locations', $package->data ?? [])) {
            $location_ids = $package->data['locations'];
        }

        $placeholdersInfo = [
            'key' => 'content',
            'type' => 'content',
            'label' => __("admin.available_placeholders"),
            'description' => Arr::join($placeholders, ', '),
            'rules' => ['nullable'],
            'is_configurable' => false,
        ];

        $buttons = array_merge([
            [
                'key' => 'locations[]',
                'name' => 'Locations',
                'description' => 'Select the locations that the server can be deployed to.',
                'type' => 'select',
                'multiple' => true,
                "options" => pteroHelper()->locationsOptions(),
                'default_value' => $location_ids,
                'rules' => ['required', 'integer'],
                'required' => true,
                'is_configurable' => true,
            ],
            [
                'key' => 'egg',
                'name' => 'Egg',
                'description' => 'Select the Nest that this server will be grouped under.',
                'type' => 'select',
                'save_on_change' => true,
                'options' => pteroHelper()::eggsOptions(),
                'default_value' => $egg_id,
                'rules' => ['required'],
                'required' => true,
                'is_configurable' => false,
            ],
        ], $serverBtn, [$placeholdersInfo], $variablesBtn);

        // Remove rules if placeholder exist
        foreach ($buttons as $key => $button) {
            if (request()->has('environment') and isset($button['env_variable'])) {
                if (in_array(request()->input('environment')[$button['env_variable']], $placeholders)) {
                    $buttons[$key]['rules'] = [];
                }
            }
        }
        return array_merge($buttons, [
            pteroHelper()::getExcludeOptions($variablesBtn)
        ], pteroHelper()::getPermissionsOptions(self::permissions()));
    }

    public static function setCheckoutConfig(Package $package): array
    {
        return pteroHelper()::getFrontendOptions($package);
    }

    public static function setServiceButtons(Order $order): array
    {
        $permissions = collect($order->package->data('permissions', []));

        // Login button
        $login_btn = [];
        if ($permissions->get('pterodactyl.login', 0) == 1 && settings('encrypted::pterodactyl::sso_secret')) {
            $login_btn = [
                "name" => __('client.login_to_panel'),
                "icon" => '<i class="bx bx-user"></i>',
                "color" => "primary",
                "href" => route('pterodactyl.login', $order->id),
                "target" => "_blank",
            ];
        }

        // Console button
        $console_btn = [];
        if ($permissions->get('pterodactyl.console', 0) == 1) {
            $console_btn = [
                "name" => __('client.console'),
                "color" => "primary",
                "icon" => "<i class='bx bx-terminal'></i>",
                "href" => route('pterodactyl.console', $order->id)
            ];
        }

        // IP button
        $ip_btn = [];
        if (settings('pterodactyl::ip_button', true)) {
            $ip = trim(ptero()::serverIP($order->id));
            if ($ip) {
                $ip_btn = [
                    "tag" => 'button',
                    "name" => $ip,
                    "color" => "primary",
                    "onclick" => "copyToClipboard(this)",
                ];
            }
        }

        return [$login_btn, $console_btn, $ip_btn];
    }

    public static function permissions(): array
    {
        return [
            'pterodactyl.login' => [
                'description' => 'Can this user automatically login to Pterodactyl',
            ],
            'pterodactyl.console' => [
                'description' => 'Full access to the console',
                'contains' => true
            ],
            'pterodactyl.files' => [
                'description' => 'Full access to the file manager',
                'contains' => true
            ],
            'pterodactyl.databases' => [
                'description' => 'Full access to the databases manager',
                'contains' => true
            ],
            'pterodactyl.schedules' => [
                'description' => 'Full access to the schedules manager',
                'contains' => true
            ],
            'pterodactyl.backups' => [
                'description' => 'Full access to the backups manager',
                'contains' => true
            ],
            'pterodactyl.network' => [
                'description' => 'Full access to the network manager',
                'contains' => true
            ],
            'pterodactyl.settings' => [
                'description' => 'Full access to the settings manager',
                'contains' => true
            ],
            'pterodactyl.variables' => [
                'description' => 'Allow user to modify egg variables',
                'contains' => true
            ],
            'pterodactyl.plugins' => [
                'description' => 'Allow user to install plugins. Is alfa version and can be unstable',
                'contains' => true
            ],
            'pterodactyl.mods' => [
                'description' => 'Allow user to install mods. Is alfa version and can be unstable',
                'contains' => true
            ],
        ];
    }


    // Service Actions & Methods
    public function create(array $data = []): void
    {
        $server = new Server($this->order);
        $data = $server->create();
        if (is_array($data) and array_key_exists('attributes', $data)) {
            $this->order->setExternalId($data['attributes']['identifier']);
        }
    }

//    public function upgrade(Package $oldPackage, Package $newPackage): void
//    {
//        $server = ptero()::server($this->order->id);
//        ptero()->api()->servers->build($server['id'], [
//            "allocation" => $server['allocation'],
//            'memory' => (integer)$newPackage->data('memory_limit', 0),
//            'swap' => (integer)$newPackage->data('swap_limit', 0),
//            'disk' => (integer)$newPackage->data('disk_limit', 0),
//            'io' => (integer)$newPackage->data('block_io_weight', 500),
//            'cpu' => (integer)$newPackage->data('cpu_limit', 100),
//            "feature_limits" => [
//                "databases" => (integer)$newPackage->data('database_limit', 0),
//                "backups" => (integer)$newPackage->data('backup_limit', 0),
//                "allocations" => (integer)$newPackage->data('allocation_limit', 0),
//            ]
//        ]);
//    }

    public function suspend(array $data = []): void
    {
        try {
            $server = ptero()::server($this->order->id);
            ptero()->api()->servers->suspend($server['id']);
        } catch (Exception $e) {
            request()->session()->flash('error', $e->getMessage());
        }
    }

    public function unsuspend(array $data = []): void
    {
        try {
            $server = ptero()::server($this->order->id);
            ptero()->api()->servers->unsuspend($server['id']);
        } catch (Exception $e) {
            request()->session()->flash('error', $e->getMessage());
        }
    }

    public function terminate(array $data = []): void
    {
        try {
            $server = ptero()::server($this->order->id);
            ptero()->api()->servers->delete($server['id']);
        } catch (Exception $e) {
            request()->session()->flash('error', $e->getMessage());
        }
    }

    // Events & Hooks
    public function eventLoadPackage(Package $package): void
    {
        ptero()::clearCache();
    }

    public function eventCheckout(Package $package): array
    {
        $location_id = 0;
        if (!request()->has('location')) {
            if (request()->has('custom_option') and array_key_exists('locations', request()->input('custom_option', []))) {
                $location_id = request()->input('custom_option')['locations'] ?? 0;
            }
        } else {
            $location_id = request()->input('location', 0);
        }

        foreach (Node::getByLocationsIds([$location_id]) as $node) {
            if (!empty($node)) {
                $resp = Node::getNodeStatus($node, ['memory' => $package->data('memory_limit', 100), 'disk' => $package->data('disk_limit', 100)]);
                if (!$resp['is_full']) {
                    return ['success' => true];
                }
            }
        }
        ErrorLog('pterodactyl::eventCheckout', "All nodes of the selected location are full. Package: $package->name", 'INFO');
        redirect()->route('store.package', $package->id)->withError(__('responses.all_nodes_full_in_location'))->send();
        return ['error' => __('responses.all_nodes_full_in_location')];
    }

}
