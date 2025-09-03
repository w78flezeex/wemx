<?php

namespace App\Services\Pterodactyl\Entities;

use App\Models\Package;

class ServiceHelper
{
    /**
     * Returns a list of options for package settings
     *
     * @param int $egg_id
     * @return array
     */
    public static function getServerParamsOptions(int $egg_id): array
    {
        $egg = Egg::getEggById($egg_id);
        if (empty($egg)) {
            $egg = collect(Egg::allEggs())->first();
        }
        return [
            [
                'col' => 'col-4',
                'key' => 'database_limit',
                'name' => 'Database Limit',
                'description' => 'The total number of databases a user is allowed to create for this server on Pterodactyl Panel.',
                'type' => 'number',
                'default_value' => '',
                'rules' => ['required', 'integer'],
                'required' => true,
                'is_configurable' => true,
            ],
            [
                'col' => 'col-4',
                'key' => 'allocation_limit',
                'name' => 'Allocation Limit',
                'description' => 'The total number of allocations a user is allowed to create for this server Pterodactyl Panel.',
                'type' => 'number',
                'default_value' => '',
                'rules' => ['required', 'integer'],
                'required' => true,
                'is_configurable' => true,
            ],
            [
                'col' => 'col-4',
                'key' => 'backup_limit',
                'name' => 'Backup Limit',
                'description' => 'The total number of backups that can be created for this server Pterodactyl Panel.',
                'type' => 'number',
                'default_value' => '',
                'rules' => ['required', 'integer'],
                'required' => true,
                'is_configurable' => true,
            ],

            [
                'col' => 'col-4',
                'key' => 'cpu_limit',
                'name' => 'CPU Limit',
                'description' => 'If you do not want to limit CPU usage, set the value to0. To use a single thread set it to 100%, for 4 threads set to 400% etc',
                'type' => 'number',
                'default_value' => '',
                'rules' => ['required', 'integer'],
                'required' => true,
                'is_configurable' => true,
            ],
            [
                'col' => 'col-4',
                'key' => 'memory_limit',
                'name' => 'Memory',
                'description' => 'The maximum amount of memory allowed for this container. Setting this to 0 will allow unlimited memory in a container.',
                'type' => 'number',
                'default_value' => '',
                'rules' => ['required', 'integer'],
                'required' => true,
                'is_configurable' => true,
            ],
            [
                'col' => 'col-4',
                'key' => 'disk_limit',
                'name' => 'Disk',
                'description' => 'The maximum amount of memory allowed for this container. Setting this to 0 will allow unlimited memory in a container.',
                'type' => 'number',
                'default_value' => '',
                'rules' => ['required', 'integer'],
                'required' => true,
                'is_configurable' => true,
            ],

            [
                'col' => 'col-4',
                'key' => 'cpu_pinning',
                'name' => 'CPU Pinning (optional) ',
                'description' => 'Advanced: Enter the specific CPU threads that this process can run on, or leave blank to allow all threads. This can be a single number, or a comma separated list. Example: 0, 0-1,3, or 0,1,3,4.',
                'type' => 'text',
                'default_value' => '',
                'rules' => ['nullable', 'string'],
                'required' => false
            ],
            [
                'col' => 'col-4',
                'key' => 'swap_limit',
                'name' => 'Swap',
                'description' => 'Setting this to 0 will disable swap space on this  Setting to -1 will allow unlimited swap.',
                'type' => 'number',
                'default_value' => '0',
                'rules' => ['nullable', 'integer'],
                'required' => false
            ],
            [
                'col' => 'col-4',
                'key' => 'block_io_weight',
                'name' => 'Block IO Weight',
                'description' => 'Advanced: The IO performance of this server relative to other running containers on the system. Value should be between 10 and 1000. Please see this documentation for more information about it.',
                'type' => 'number',
                'default_value' => '',
                'rules' => ['nullable', 'integer'],
                'required' => false
            ],

            [
                'col' => 'col-4',
                'key' => 'docker_image',
                'name' => 'Docker Image',
                'description' => 'This is the default Docker image that will be used to run this  Select an image from the dropdown above, or enter a custom image in the text field.',
                'type' => 'select',
                'options' => is_array($egg['docker_images']) ? array_flip($egg['docker_images']) : ['default' => $egg['docker_image']],
                'default_value' => $egg['docker_image'],
                'rules' => ['required', 'string'],
                'required' => true,
                'is_configurable' => true,
            ],
            [
                'col' => 'col-4',
                'key' => 'oom_disabled',
                'name' => 'Enable OOM Killer',
                'description' => 'Terminates the server if it breaches the memory limits.',
                'type' => 'bool',
                'default_value' => 0,
                'rules' => ['boolean'],
                'required' => false
            ],
            [
                'col' => 'col-4',
                'key' => 'start_on_completion',
                'name' => 'Start on Completion',
                'description' => 'Automatically start the server after creation',
                'type' => 'bool',
                'default_value' => 1,
                'rules' => ['boolean'],
                'required' => false
            ],
            [
                'col' => 'col-12',
                'key' => 'startup',
                'name' => 'Startup Command',
                'description' => 'The following data substitutes are available for the startup command: {{SERVER_MEMORY}},{{SERVER_IP}}, and {{SERVER_PORT}}}. They will be replaced with the allocated memory, server IP, and server port respectively.',
                'type' => 'text',
                'default_value' => $egg['startup'],
                'rules' => ['required', 'string'],
                'required' => true,
                'is_configurable' => true,
            ],

        ];
    }

    /**
     * Returns a select options of variable exceptions
     *
     * @param array $variables
     * @return array
     */
    public static function getExcludeOptions(array $variables): array
    {
        $options = [];
        foreach ($variables as $variable) {
            if (preg_match('/\[(.*?)\]/', $variable['key'], $matches)) {
                $variable['key'] = $matches[1];
            }
            $options[$variable['key']] = $variable['name'];
        }
        return [
            'col' => 'col-12',
            'key' => 'excluded_variables[]',
            'name' => 'Exclude variables from checkout',
            'description' => 'Select variables you do not want users to be able to modify at checkout',
            'type' => 'select',
            'multiple' => true,
            "options" => $options,
            'default_value' => '',
            'rules' => ['nullable', 'string'],
            'required' => false
        ];
    }

    /**
     * Returns the list of options that are displayed for the client
     *
     * @param Package $package
     * @return array
     */
    public static function getFrontendOptions(Package $package): array
    {
        $buttons = self::variablesToOptions(is_numeric($package->data['egg']) ? $package->data['egg'] : json_decode($package->data['egg'], true)['id']);
        $excludeVariables = $package->data['excluded_variables'] ?? [];
        $data = [];
        $locations = [
            'key' => 'location',
            'name' => 'Location',
            'description' => 'Select the location',
            'type' => 'select',
            "options" => self::locationsOptions($package->data['locations']),
            'default_value' => ' ',
            'rules' => ['required', 'integer'],
            'required' => true,
        ];

        try {
            $configOptions = $package->configOptions()->get();
            // Check if the 'locations' configuration option exists
            if (!$configOptions->contains('key', 'locations')) {
                // If 'locations' is in $package->data, add to $data
                if (array_key_exists('locations', $package->data ?? [])) {
                    $data[] = $locations;
                }
            }

            $customOptions = $configOptions->filter(fn($item) => str_starts_with($item->key, 'environment'))->map(function ($item) {
                    // Update the 'key' directly
                    $item->key = str_replace('environment', 'environment[', $item->key) . ']';
                    return $item;
                })->values()->keyBy('key')->toArray();
        } catch (\Exception $e) {
            $customOptions = [];
            $data[] = $locations;
        }

        foreach ($buttons as $item) {
            if (array_key_exists($item['key'], $customOptions)) {
                continue;
            }
            if (preg_match('/\[(.*?)\]/', $item['key'], $matches)) {
                $item['key'] = $matches[1];
            }
            if (in_array($item['key'], $excludeVariables)) {
                continue;
            }
            if (array_key_exists($item['key'], $package->data['environment'] ?? [])) {
                $item['default_value'] = getValueByKey($item['key'], $package->data, $package->data['environment'][$item['key']] ?? '');
                $item['key'] = "environment[{$item['key']}]";
                $data[] = $item;
            }
        }
        return $data;
    }

    public static function getFrontendAllowedKeys(Package $package): array
    {
        $allowedClientOptions = collect(self::getFrontendOptions($package))->pluck('key')->mapToGroups(function ($key) {
            // We check whether the key is in the format environment[...]
            if (preg_match('/^environment\[(.*)\]$/', $key, $matches)) {
                $envKey = $matches[1];
                return ['environment' => [$envKey => true]];
            } else {
                return ['topLevel' => [$key => true]];
            }
        })->map(function ($group, $key) {
            // We turn collections into arrays
            return $group->collapse()->all();
        })->all();

        // Remove the topLevel key, if it exists, to leave only top-level keys
        if (isset($allowedClientOptions['topLevel'])) {
            $allowedClientOptions = array_merge($allowedClientOptions['topLevel'], $allowedClientOptions);
            unset($allowedClientOptions['topLevel']);
        }
        return $allowedClientOptions;
    }

    /**
     * The method turns locations into options for selection
     *
     * @param array $ids
     * @return array
     */
    public static function locationsOptions(array $ids = []): array
    {
        $transformedArray = [];
        $useShort = settings('pterodactyl::short_location_name', false);
        $allLocations = app(Location::class)->allLocations();
        if (empty($ids)) {
            foreach ($allLocations as $item) {
                $key = $item['id'];
                $value = parseLocationName($useShort, $item['short'] ?? '', $item['long'] ?? '');
                $value = $item['is_full'] ? $value . ' ' . __('admin.location_full') : $value;
                $transformedArray[$key] = ['name' => $value, 'disabled' => $item['is_full'] ?? false];
            }
        } else {
            foreach ($allLocations as $item) {
                if (in_array($item['id'], $ids)) {
                    $key = $item['id'];
                    $value = parseLocationName($useShort, $item['short'] ?? '', $item['long'] ?? '');
                    $value = $item['is_full'] ? $value . ' ' . __('admin.location_full') : $value;
                    $transformedArray[$key] = ['name' => $value, 'disabled' => $item['is_full'] ?? false];
                }
            }
        }
        return $transformedArray;
    }

    /**
     * The method turns eggs into options for selection
     *
     * @return array
     */
    public static function eggsOptions(): array
    {
        $transformedArray = [];
        foreach (Egg::allEggs() as $item) {
            $key = $item['id'];
            $value = $item['name'];
            $transformedArray[$key] = $value;
        }
        return $transformedArray;
    }

    /**
     * We convert egg variables into select options
     *
     * @param int $egg_id
     * @return array
     */
    public static function variablesToOptions(int $egg_id): array

    {
        $variables = Egg::getEggById($egg_id)['variables'] ?? [];
        $data = [];
        foreach ($variables as $variable) {
            $rules = explode('|', $variable['rules']);
            $prepare = PteroUtil::determineType($rules);
            $options = [];
            $options['required'] = in_array('required', $rules);
            if (array_key_exists('options', $prepare)) {
                $options['options'] = $prepare['options'];
            }
            if (array_key_exists('max', $prepare)) {
                $options['max'] = $prepare['max'];
            }
            if (array_key_exists('min', $prepare)) {
                $options['min'] = $prepare['min'];
            }

            if (in_array($variable['env_variable'], self::getConfigurableVariables())) {
                $is_configurable = true;
            } else {
                $is_configurable = false;
            }
            $data[$variable['id']] = array_merge($variable, [
                'key' => 'environment[' . $variable['env_variable'] . ']',
                'type' => $prepare['type'],
                'rules' => $rules,
                'is_configurable' => $is_configurable,
            ], $options);
        }
        return $data;
    }

    public static function getConfigurableVariables(): array
    {
        return ['SRCDS_MAXPLAYERS', 'SERVER_TICKRATE', 'MAX_PLAYERS', 'TICKRATE', 'MINECRAFT_VERSION'];
    }

    public static function getPermissionsOptions(array $permissions): array
    {
        $data = [];
        foreach ($permissions as $key => $permission) {
            $data[] = [
                'col' => 'col-4',
                'key' => 'permissions[' . $key . ']',
                'name' => strtoupper(str_replace('pterodactyl.', '', $key)),
                'description' => $permission['description'],
                'type' => 'bool',
                'default_value' => 0,
                'rules' => ['boolean'],
                'required' => false
            ];
        }
        return $data;
    }
}
