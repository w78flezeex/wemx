<?php

return [

    'name' => 'DiscordWebhooks',
    'icon' => 'https://imgur.png',
    'author' => 'WemX',
    'version' => '1.0.4',
    'wemx_version' => '>=2.2.0',

    'elements' => [

        'admin_menu' =>
        [
            [
                'name' => 'Discord Webhooks',
                'icon' => '<i class="fab fa-discord"></i>',
                'href' => '/admin/discord-webhooks',
                'style' => '',
            ],
        ],
    ]
];
