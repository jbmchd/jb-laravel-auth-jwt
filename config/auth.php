<?php

return [

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'usuarios',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'usuarios',
        ],

        'api' => [
            'driver' => 'jwt',
            'provider' => 'usuarios',
            'hash' => false,
        ],
    ],

    'providers' => [
        'usuarios' => [
            'driver' => 'eloquent',
            'model' => \JbGlobal\Models\Pessoa::class,
        ],
    ],

    'passwords' => [
        'usuarios' => [
            'provider' => 'usuarios',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],


];
