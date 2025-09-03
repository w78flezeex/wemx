<?php

namespace App\Services\CloudFlare\Entities;

use App\Services\CloudFlare\Sdk\Adapter\Guzzle;
use App\Services\CloudFlare\Sdk\Auth\APIKey;
use App\Services\CloudFlare\Sdk\Endpoints\DNS;
use App\Services\CloudFlare\Sdk\Endpoints\Zones;
use Illuminate\Support\Collection;

class CfHelper
{
    public static function apiKey(): APIKey
    {
        return new APIKey(settings('cloudflare::email', ''), settings('encrypted::cloudflare::api_key', ''));
    }

    public static function adapter(): Guzzle
    {
        return new Guzzle(self::apiKey());
    }

    public static function dns(): DNS
    {
        return new DNS(self::adapter());
    }

    public static function findRecordByName($name, $zone_name = ''): array
    {
        $zones = self::getCachedZones();
        foreach ($zones as $zone) {
            if ($zone->name === $zone_name) {
                $records = self::dns()->listRecords($zone->id)->result;
                foreach ($records as $record) {
                    if ($record->name == $name) {
                        return json_decode(json_encode($record), true);
                    }
                }
            }
        }
        return [];
    }

    public static function findRecordByOrderId($id, $zone_name = ''): array
    {
        $zones = self::getCachedZones();
        $data = [];
        foreach ($zones as $zone) {
            if ($zone->name === $zone_name) {
                $records = self::dns()->listRecords($zone->id, comment: $id)->result;
                foreach ($records as $record) {
                    $data[$record->type] = json_decode(json_encode($record), true);
                }
            }
        }
        return $data;
    }

    public static function getDomainsList(): Collection
    {
        $zones = self::getCachedZones();
        $domains = [];
        foreach ($zones as $zone) {
            $domains[$zone->id] = $zone->name;
        }
        return collect($domains);
    }

    public static function srvExist($subdomain, $domain): bool
    {
        $zones = self::getCachedZones();
        foreach ($zones as $zone) {
            $records = self::dns()->listRecords($zone->id);
            foreach ($records->result as $record) {
                if ($record->name == $subdomain . '.' . $domain) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function createSrvDNSRecords(PterodactylOrderDomains|WispOrderDomains $data): void
    {
        self::dns()->addRecord(
            zoneID: $data->domain_data['id'],
            type: 'A',
            name: $data->domain_data['subdomain'],
            content: $data->domain_data['ip'],
            proxied: false,
            comment: $data->order_id
        );
        $srvData = [
            'name' => $data->domain_data['subdomain'],
            'service' => $data->domain_data['service'],
            'proto' => $data->domain_data['proto'],
            'priority' => 0,
            'weight' => 0,
            'port' => $data->domain_data['port'],
            'target' => $data->domain_data['subdomain'] . '.' . $data->domain_data['domain']
        ];
        self::dns()->addRecord(
            zoneID: $data->domain_data['id'],
            type: 'SRV',
            name: $data->domain_data['service'] . '.' . $data->domain_data['proto'] . '.' . $data->domain_data['subdomain'],
            content: $data->domain_data['subdomain'] . '.' . $data->domain_data['domain'],
            proxied: false,
            priority: 0,
            comment: $data->order_id,
            data: $srvData
        );
        $all = self::findRecordByOrderId($data->order_id, $data->domain_data['domain']);
        if (array_key_exists('A', $all) && array_key_exists('SRV', $all)) {
            $cf['sub'] = $all['A'];
            $cf['srv'] = $all['SRV'];
            $data->domain_data = array_merge($data->domain_data, $cf);
            $data->save();
        } else {
            $cf['sub'] = self::findRecordByName(self::getSubDomainName($data->domain_data), $data->domain_data['domain']);
            $cf['srv'] = self::findRecordByName(self::getSrvName($data->domain_data), $data->domain_data['domain']);
            $data->domain_data = array_merge($data->domain_data, $cf);
            $data->save();
        }
    }

    public static function deleteSrvDNSRecords(PterodactylOrderDomains|WispOrderDomains $data): void
    {
        foreach (self::dns()->listRecords($data->domain_data['id'], comment: $data->order_id)->result as $record) {
            if ($record->name == self::getSubDomainName($data->domain_data)) {
                self::dns()->deleteRecord($data->domain_data['id'], $record->id);
            }
            if ($record->name == self::getSrvName($data->domain_data)) {
                self::dns()->deleteRecord($data->domain_data['id'], $record->id);
            }
        }
    }


    public static function getCachedZones(): Collection
    {
        return cache()->remember('cloudflare.zones', 600, function () {
            $zones = new Zones(self::adapter());
            $response = $zones->listZones();
            return collect($response->result);
        });
    }

    public static function getTypeData($type = 'minecraft'): array
    {
        $types = [
            'minecraft' => [
                'service' => '_minecraft',
                'proto' => '_tcp',
                'name' => 'Minecraft',
            ],
            'bedrock' => [
                'service' => '_minecraft',
                'proto' => '_udp',
                'name' => 'Minecraft Bedrock',
            ],
            'ts3' => [
                'service' => '_ts3',
                'proto' => '_udp',
                'name' => 'Teamspeak 3',
            ],
            'csgo' => [
                'service' => '_csgo',
                'proto' => '_udp',
                'name' => 'Counter-Strike: Global Offensive',
            ],
            'rust' => [
                'service' => '_rust',
                'proto' => '_tcp',
                'name' => 'Rust',
            ],
            'gmod' => [
                'service' => '_gmod',
                'proto' => '_udp',
                'name' => 'Garry\'s Mod',
            ],
            'ark' => [
                'service' => '_ark',
                'proto' => '_tcp',
                'name' => 'ARK: Survival Evolved',
            ],
            'factorio' => [
                'service' => '_factorio',
                'proto' => '_tcp',
                'name' => 'Factorio',
            ],
            'terraria' => [
                'service' => '_terraria',
                'proto' => '_tcp',
                'name' => 'Terraria',
            ],
            'squad' => [
                'service' => '_squad',
                'proto' => '_udp',
                'name' => 'Squad',
            ],
            'arma3' => [
                'service' => '_arma3',
                'proto' => '_udp',
                'name' => 'Arma 3',
            ],
            'fivem' => [
                'service' => '_fivem',
                'proto' => '_udp',
                'name' => 'FiveM',
            ],
            'other_tcp' => [
                'service' => '_other',
                'proto' => '_tcp',
                'name' => 'Other TCP',
            ],
            'other_udp' => [
                'service' => '_other',
                'proto' => '_udp',
                'name' => 'Other UDP',
            ],
        ];
        if ($type == 'all') {
            return $types;
        }
        return $types[$type];

    }

    public static function getSrvName(array $data): string
    {
        $service = $data['service'] ?? '_minecraft';
        $proto = $data['proto'] ?? '_tcp';
        return $service . '.' . $proto . '.' . $data['subdomain'] . '.' . $data['domain'];
    }

    public static function getSubDomainName(array $data): string
    {
        return $data['subdomain'] . '.' . $data['domain'];
    }
}
