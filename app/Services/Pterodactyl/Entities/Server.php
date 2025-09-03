<?php

namespace App\Services\Pterodactyl\Entities;

use App\Models\Order;
use App\Services\Pterodactyl\Api\Pterodactyl;

class Server
{

    private Order $order;
    private array $orderOptions;
    private Node $node;
    private array $egg;
    private array $serverOptions = [];

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->prepare();
    }

    public function api(): Pterodactyl
    {
        return app(PteroUtil::class)->api();
    }

    public function getPreparedOptions(): array
    {
        return $this->serverOptions;
    }

    public function create(): mixed
    {
        return $this->api()->servers->create($this->serverOptions);
    }

    private function prepare(): void
    {
        $package_options = $this->order->package['data'];
        $client_options = $this->order->options;

        if (!array_key_exists('custom_option', $client_options)) {
            $client_options['custom_option'] = [];
        }

        // Location support from custom options
        if (!array_key_exists('location', $client_options) and array_key_exists('custom_option', $client_options)) {
            if (array_key_exists('locations', $client_options['custom_option'])) {
                $package_options['location'] = $client_options['custom_option']['locations'];
            }
        }

        // Remove is prohibited variables if they exist in the data from the user
        $client_options['environment'] = array_diff_key($client_options['environment'] ?? [], array_flip($package_options['excluded_variables'] ?? []));

        // Replacing standard values with user values
        $package_options['environment'] = array_replace_recursive($package_options['environment'], $client_options['environment']);

        // If the user has custom options, we replace the standard values with the user values and add them to the allowed keys
        $allowedKeys = pteroHelper()->getFrontendAllowedKeys($this->order->package);
        foreach ($this->order->package->configOptions()->get() as $value) {
            $key = str_replace('environment', '', $value->key);
            if (array_key_exists($key, $client_options['custom_option'])) {
                $allowedKeys[$value->key] = true;
                $client_options[$value->key] = $client_options['custom_option'][$value->key];
                continue;
            }
            if (empty($client_options['custom_option'])) {
                continue;
            }
            if ($key !== $value->key) {
                $allowedKeys[$value->key] = true;
                $client_options['environment'][$key] = $client_options['custom_option'][$value->key];
            }
        }

        // Remove unused variables
        unset($client_options['coupon']);
        unset($client_options['custom_option']);
        unset($client_options['custom_options_modifiers']);
        unset($package_options['excluded_variables']);
        unset($package_options['permissions']);

        // Remove all keys that are not allowed
        $client_options = array_intersect_key($client_options, $allowedKeys);

        $this->orderOptions = array_replace_recursive($package_options, $client_options);

        $this->findNode();
        $this->findEgg();

        $this->serverOptions = array_merge([
            'external_id' => 'wmx-' . $this->order->id,
            "name" => $this->order->name,
            'description' => settings('app_name', 'WemX') . " || {$this->order->name} || {$this->order->user->username}",
            "user" => (int)$this->getUserId(),
            "egg" => (int)$this->orderOptions['egg'],
            'oom_disabled' => (bool)$this->orderOptions['oom_disabled'],
            "docker_image" => $this->orderOptions['docker_image'],
            "startup" => $this->orderOptions['startup'],
            "limits" => $this->getLimits(),
            "feature_limits" => $this->getFeatureLimits(),
            'start_on_completion' => (bool)$this->orderOptions['start_on_completion'],
        ],
            Placeholder::prepareEnvAllocations($this->orderOptions['environment'], $this->node, $this->egg) // add env and allocations
        );
    }

    private function getLimits(): array
    {
        return [
            "memory" => (integer)$this->orderOptions['memory_limit'] ?? 0,
            "swap" => (integer)$this->orderOptions['swap_limit'] ?? 0,
            "disk" => (integer)$this->orderOptions['disk_limit'] ?? 0,
            "io" => empty($this->orderOptions['block_io_weight']) ? 500 : (integer)$this->orderOptions['block_io_weight'],
            "cpu" => (integer)$this->orderOptions['cpu_limit'] ?? 100,
        ];
    }

    private function getFeatureLimits(): array
    {
        return [
            "databases" => (integer)$this->orderOptions['database_limit'] ?? 0,
            "backups" => (integer)$this->orderOptions['backup_limit'] ?? 0,
            "allocations" => (integer)$this->orderOptions['allocation_limit'] ?? 0,
        ];
    }

    private function findNode(): void
    {
        // get all nodes selected location and if all node full return redirect back with error
        foreach (Node::getByLocationsIds([$this->orderOptions['location']]) as $node) {
            if (!empty($node)) {
                $resp = Node::getNodeStatus($node, ['memory' => $this->orderOptions['memory_limit'], 'disk' => $this->orderOptions['disk_limit']]);
                if (!$resp['is_full']) {
                    $this->node = new Node($node['id']);
                    break;
                }
            }
        }
        if (empty($this->node)) {
            ErrorLog('pterodactyl::Server::findNode', "Could not locate node with location id {$this->orderOptions['location']} on Pterodactyl.", 'CRITICAL');
            redirect()->back()->with('error', __('responses.no_available_nodes'))->send();
        }
    }

    private function findEgg(): void
    {
        // If old package data
        if (is_array($this->orderOptions['egg'])) {
            $this->orderOptions['egg'] = (int)$this->orderOptions['egg']['id'];
        } else {
            $this->orderOptions['egg'] = (int)$this->orderOptions['egg'];
        }
        $this->egg = Egg::getEggById($this->orderOptions['egg']);
        if (empty($this->egg)) {
            ErrorLog('pterodactyl::Server::findEgg', "Could not locate egg with id {$this->orderOptions['egg']} on Pterodactyl.", 'CRITICAL');
            redirect()->back()->with('error', __('responses.no_available_egg_in_order'))->send();
        }
    }

    private function getUserId()
    {
        // get user data, create user if not exist
        return ptero()->user()->get($this->order->user)['id'];
    }

}
