<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Achievement System Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the achievement system settings
    |
    */

    'enabled' => env('ACHIEVEMENT_SYSTEM_ENABLED', true),

    'points_multiplier' => env('ACHIEVEMENT_POINTS_MULTIPLIER', 1),

    'auto_check' => env('ACHIEVEMENT_AUTO_CHECK', true),

    'notifications' => [
        'enabled' => env('ACHIEVEMENT_NOTIFICATIONS', true),
        'email' => env('ACHIEVEMENT_EMAIL_NOTIFICATIONS', true),
        'in_app' => env('ACHIEVEMENT_IN_APP_NOTIFICATIONS', true),
    ],

    'achievements' => [
        'first_order' => [
            'name' => 'Первый заказ',
            'description' => 'Создайте свой первый заказ',
            'points' => 100,
            'icon' => 'fas fa-shopping-cart',
            'requirements' => [
                'type' => 'first_order',
                'count' => 1
            ]
        ],
        'order_count' => [
            'name' => 'Постоянный клиент',
            'description' => 'Создайте 10 заказов',
            'points' => 500,
            'icon' => 'fas fa-star',
            'requirements' => [
                'type' => 'order_count',
                'count' => 10
            ]
        ],
        'payment_methods' => [
            'name' => 'Разнообразие',
            'description' => 'Используйте 3 различных способа оплаты',
            'points' => 300,
            'icon' => 'fas fa-credit-card',
            'requirements' => [
                'type' => 'payment_methods',
                'count' => 3
            ]
        ]
    ]
];
