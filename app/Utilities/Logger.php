<?php

namespace App\Utilities;

use App\Core\Request;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

class Logger
{
    public static ?MonologLogger $logger = null;

    /**
     * Create a new Monolog logger instance.
     *
     * @param string $name
     * @return MonologLogger
     */
    public static function get(string $name = ''): MonologLogger
    {
        $request = new Request();
        $requestUri = $request->getUri();

        if (self::$logger === null) {
            self::$logger = new MonologLogger($requestUri);
            self::$logger->pushHandler(
                new StreamHandler(ROOT . '/storage/logs/' . date('m-d-Y') . '.log')
            );
        }

        return self::$logger;
    }

    public function __clone() {}
    public function __wakeup() {}
}