<?php

return [

    'name' => 'Tickets Module',
    'icon' => 'https://imgur.png',
    'author' => 'WemX',
    'version' => '1.5.0',
    'wemx_version' => '2.2.0',

    'elements' => [

        'main_menu' =>
        [
            [
                'name' => 'Тикеты',
                'icon' => "<i class='bx bxs-chat' ></i>",
                'href' => '/tickets',
                'style' => '',
            ],
        ],

        'apps' =>
        [
            [
                'name' => 'Тикеты',
                'icon' => "<i class='bx bxs-chat' ></i>",
                'href' => '/tickets',
                'style' => '',
            ],
        ],

        'admin_menu' =>
        [

            [
                'name' => 'Тикеты',
                'icon' => '<i class="fas fa-ticket-alt"></i>',
                'type' => 'dropdown',
                'items' => [
                    [
                        'name' => 'Настройки',
                        'href' => '/admin/tickets/settings',
                    ],
                    [
                        'name' => 'Тикеты',
                        'href' => '/admin/tickets',
                    ],

                    [
                        'name' => 'Отделы',
                        'href' => '/admin/tickets/departments',
                    ],

                    [
                        'name' => 'Ответчики',
                        'href' => '/admin/tickets/responders',
                    ],
                ],
            ],

        ],
    ],

];
