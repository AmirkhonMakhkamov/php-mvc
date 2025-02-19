<?php

namespace App\Utilities;

use Dotenv\Dotenv;
use InvalidArgumentException;

class Env
{
    /**
     * Load environment variables from a file
     *
     * @return void
     */
    public static function load(): void
    {
        $envFile = self::getEnvironmentFile();

        if (!file_exists($envFile)) {
            throw new InvalidArgumentException("Environment file $envFile does not exist.");
        }

        $dotenv = Dotenv::createImmutable(dirname($envFile), basename($envFile));
        $dotenv->load();
    }

    /**
     * Get the environment file based on the environment
     *
     * @return string
     */
    private static function getEnvironmentFile(): string
    {
        $config = require ROOT . '/app/config/config.php';
        $environment = $config['app']['environment'];

        return match ($environment) {
            'production' => ROOT . '/.env.production',
            'staging' => ROOT . '/.env.staging',
            default => ROOT . '/.env',
        };
    }

    /**
     * Get an environment variable
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function get(string $key, string $default = ''): string
    {
        $value = $_ENV[$key] ?? $default;
        return self::sanitize($value);
    }

    /**
     * Sanitize the value
     *
     * @param string $value
     * @return string
     */
    private static function sanitize(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}