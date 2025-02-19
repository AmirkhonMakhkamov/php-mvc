<?php
const ROOT = __DIR__ . '/..';

require_once ROOT . '/bootstrap/app.php';

try {
    /** @var App\Core\Router $router */
    /** @var App\Core\Request $request */
    /** @var App\Core\Response $response */

    $app = new App\Core\App($router, $request, $response);
    $app->run();

} catch (Exception $e) {
    die($e->getMessage());
}