<?php

require_once ROOT . '/vendor/autoload.php';

$container = new App\Core\Container();

require_once ROOT . '/bootstrap/services.php';

try {
    $router = $container->make('Router');
    $request = $container->make('Request');
    $response = $container->make('Response');
} catch (Exception $e) {
    die($e->getMessage());
}

require_once ROOT . '/routes/web.php';