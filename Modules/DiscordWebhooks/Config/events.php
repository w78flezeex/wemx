<?php

return [
    'order_created' => [
        'event' => \App\Events\Order\OrderCreated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderCreatedListener',
        'description' => 'Receive a notification when an order is created',
    ],
    'order_activated' => [
        'event' => \App\Events\Order\OrderActivated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderActivatedListener',
        'description' => 'Receive a notification when an order is activated',
    ],
    'order_canceled' => [
        'event' => \App\Events\Order\OrderCancelled::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderCancelledListener',
        'description' => 'Receive a notification when an order is cancelled',
    ],
    'order_deleted' => [
        'event' => \App\Events\Order\OrderDeleted::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderDeletedListener',
        'description' => 'Receive a notification when an order is deleted',
    ],
    'order_force_suspended' => [
        'event' => \App\Events\Order\OrderForceSuspended::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderForceSuspendedListener',
        'description' => '',
    ],
    'order_force_terminated' => [
        'event' => \App\Events\Order\OrderForceTerminated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderForceTerminatedListener',
        'description' => '',
    ],
    'order_renewed' => [
        'event' => \App\Events\Order\OrderRenewed::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderRenewedListener',
        'description' => 'Receive a notification when an order is renewed',
    ],
    'order_suspended' => [
        'event' => \App\Events\Order\OrderSuspended::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderSuspendedListener',
        'description' => 'Receive a notification when an order is suspended',
    ],
    'order_terminated' => [
        'event' => \App\Events\Order\OrderTerminated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderTerminatedListener',
        'description' => 'Receive a notification when an order is terminated',
    ],
    'order_unsuspended' => [
        'event' => \App\Events\Order\OrderUnsuspended::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderUnsuspendedListener',
        'description' => 'Receive a notification when an order is unsuspended',
    ],
    'order_updated' => [
        'event' => \App\Events\Order\OrderUpdated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderUpdatedListener',
        'description' => '',
    ],
    'order_upgraded' => [
        'event' => \App\Events\Order\OrderUpgraded::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OrderUpgradedListener',
        'description' => 'Receive a notification when an order is upgraded',
    ],
    'payment_paid' => [
        'event' => \App\Events\PaymentCompleted::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PaymentCompletedListener',
        'description' => 'Receive a notification when a payment is paid',
    ],
    'payment_created' => [
        'event' => \App\Events\PaymentCreated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PaymentCreatedListener',
        'description' => 'Receive a notification when a payment is created',
    ],
    'payment_deleted' => [
        'event' => \App\Events\PaymentDeleted::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PaymentDeletedListener',
        'description' => 'Receive a notification when a payment is deleted',
    ],
    'payment_refunded' => [
        'event' => \App\Events\PaymentRefunded::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PaymentRefundedListener',
        'description' => 'Receive a notification when a payment is refunded',
    ],
    'payment_updated' => [
        'event' => \App\Events\PaymentUpdated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PaymentUpdatedListener',
        'description' => '',
    ],
    'user_created' => [
        'event' => \App\Events\UserCreated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\UserCreatedListener',
        'description' => 'Receive a notification when an user is created',
    ],
    'user_deleted' => [
        'event' => \App\Events\UserDeleted::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\UserDeletedListener',
        'description' => 'Receive a notification when an user is created',
    ],
    # TODO: Start adding events
    'external_account_created' => [
        'event' => \App\Events\ExternalAccounts\AccountCreated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\ExternalAccountCreatedListener',
        'description' => 'Receive a notification when an external account is created',
    ],
    'external_account_updated' => [
        'event' => \App\Events\ExternalAccounts\AccountUpdated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\ExternalAccountUpdatedListener',
        'description' => 'Receive a notification when an external account is updated',
    ],
    'external_account_deleted' => [
        'event' => \App\Events\ExternalAccounts\AccountDeleted::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\ExternalAccountDeletedListener',
        'description' => 'Receive a notification when an external account is deleted',
    ],
    'oauth_connected' => [
        'event' => \App\Events\Oauth\OauthConnected::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OauthConnectedListener',
        'description' => 'Receive a notification when an OAuth client is created',
    ],
    'oauth_disconnected' => [
        'event' => \App\Events\Oauth\OauthDisconnected::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OauthDisconnectedListener',
        'description' => 'Receive a notification when an OAuth client is deleted',
    ],
    'oauth_updated' => [
        'event' => \App\Events\Oauth\OauthUpdated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\OauthUpdatedListener',
        'description' => 'Receive a notification when an OAuth client is updated',
    ],
    'package_created' => [
        'event' => \App\Events\Package\PackageCreated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PackageCreatedListener',
        'description' => 'Receive a notification when a package is created',
    ],
    'package_deleted' => [
        'event' => \App\Events\Package\PackageDeleted::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PackageDeletedListener',
        'description' => 'Receive a notification when a package is deleted',
    ],
    'package_updated' => [
        'event' => \App\Events\Package\PackageUpdated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PackageUpdatedListener',
        'description' => 'Receive a notification when a package is updated',
    ],
    'punishment_created' => [
        'event' => \App\Events\Punishment\PunishmentCreated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PunishmentCreatedListener',
        'description' => 'Receive a notification when a punishment is created',
    ],
    'punishment_deleted' => [
        'event' => \App\Events\Punishment\PunishmentDeleted::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PunishmentDeletedListener',
        'description' => 'Receive a notification when a punishment is deleted',
    ],
    'punishment_updated' => [
        'event' => \App\Events\Punishment\PunishmentUpdated::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\PunishmentUpdatedListener',
        'description' => 'Receive a notification when a punishment is updated',
    ],
    # END TODO
    'module_service_enabled' => [
        'event' => \App\Events\ModuleServiceEnabled::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\ModuleServiceEnabledListener',
        'description' => 'Receive a notification when a module service is enabled',
    ],
    'module_service_disabled' => [
        'event' => \App\Events\ModuleServiceDisabled::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\ModuleServiceDisabledListener',
        'description' => 'Receive a notification when a module service is disabled',
    ],
    'module_service_deleted' => [
        'event' => \App\Events\ModuleServiceDeleted::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\ModuleServiceDeletedListener',
        'description' => 'Receive a notification when a module service is deleted',
    ],
    'error_log' => [
        'event' => \App\Events\ErrorLog::class,
        'listener' => 'Modules\DiscordWebhooks\Listeners\ErrorLogListener',
        'description' => 'Receive a notification when an error log is created',
    ],
];
