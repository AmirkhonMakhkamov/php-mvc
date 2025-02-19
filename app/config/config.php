<?php

use App\Utilities\Cookie;
use App\Utilities\Env;

return [
    'app' => [
        'name' => 'My MVC Project',
        'url' => Env::get('APP_URL', 'http://localhost'),
        'environment' => 'development',
        'debug' => Env::get('APP_DEBUG', true),
        'timezone' => Env::get('APP_TIMEZONE', 'UTC'),
        'locale' => Env::get('APP_LOCALE', 'en'),
    ],
    'paths' => [
        'base' => ROOT,
        'public' => ROOT . '/public',
        'views' => ROOT . '/app/Views',
        'cache' => ROOT . '/storage/cache',
    ],
    'mail' => [
        'name' => Env::get('MAIL_NAME'),
        'address' => Env::get('MAIL_ADDRESS'),
    ],
    'session' => [
        'name' => Env::get('SESSION_NAME', 'appSession'),
        'lifetime' => Env::get('SESSION_LIFETIME', 3600),
        'path' => '/',
        'domain' => null,
        'secure' => Env::get('SESSION_SECURE_COOKIE', false),
        'httponly' => true,
    ],
];
