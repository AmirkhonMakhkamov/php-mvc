<?php

use App\Utilities\Env;

return [
    'default' => Env::get('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => Env::get('DB_HOST', '127.0.0.1'),
            'port' => Env::get('DB_PORT', '3306'),
            'username' => Env::get('DB_USERNAME', 'your_username_dev'),
            'password' => Env::get('DB_PASSWORD', 'your_password_dev'),
            'dbname' => Env::get('DB_NAME', 'your_database_dev'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'timezone' => Env::get('APP_TIMEZONE', 'UTC'),
        ],

        // Other database connections
    ],

    'migrations' => 'migrations',
];
