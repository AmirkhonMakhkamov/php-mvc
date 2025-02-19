<?php

namespace App\Utilities;

class Cookie
{
    /**
     * Set a cookie
     *
     * @param string $name
     * @param string $value
     * @param int $expiry
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return bool
     */
    public static function set(
        string $name,
        string $value,
        int $expiry = 3600,
        string $path = "/",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ): bool {
        return setcookie($name, $value, time() + $expiry, $path, $domain, $secure, $httponly);
    }

    /**
     * Get a cookie
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    public static function get(string $name, string $default = ''): string
    {
        return $_COOKIE[$name] ?? $default;
    }

    /**
     * Delete a cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return bool
     */
    public static function delete(
        string $name,
        string $path = "/",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ): bool {
        return setcookie($name, "", time() - 3600, $path, $domain, $secure, $httponly);
    }

    /**
     * Check if a cookie exists
     *
     * @param string $name
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }
}