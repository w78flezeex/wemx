<?php

return [

    'name' => 'CloudFlare',
    'icon' => 'https://imgur.png',
    'author' => 'GIGABAIT',
    'version' => '1.0.5',
    'wemx_version' => '>=2.2.0',
    'service' => \App\Services\CloudFlare\Service::class,

    'elements' => [

        'admin_menu' =>
            [

                [
                    'name' => 'CloudFlare',
                    'icon' => '<i class="fab fa-cloudflare"></i>',
                    'type' => 'dropdown',
                    'items' => [
                        [
                            'name' => 'admin.pterodactyl',
                            'href' => '/admin/cloudflare/pterodactyl',
                        ],
                        [
                            'name' => 'Wisp',
                            'href' => '/admin/cloudflare/wisp',
                        ],


                    ],
                ],

            ],

    ],

];
