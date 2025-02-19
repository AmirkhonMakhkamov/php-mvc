<?php

/** @var App\Core\Container $container */

try {
    $container->singleton('Request', function () {
        return new App\Core\Request();
    });

    $container->singleton('Response', function () {
        return new App\Core\Response();
    });

    $container->singleton('Router', function () use ($container) {
        return new App\Core\Router($container);
    });
} catch (Exception $e) {
    die($e->getMessage());
}