<?php

namespace App\Utilities;

use JetBrains\PhpStorm\NoReturn;

class Error
{
    #[NoReturn] public static function throw(int $code): void
    {
        http_response_code($code);
        include_once ROOT . "/app/Views/Errors/$code.html";
        die();
    }
}