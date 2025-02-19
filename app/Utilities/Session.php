<?php

namespace App\Utilities;

class Session
{
    public static function start(
        string $name = 'myapp_session',
        int $lifetime = 3600,
        string $path = '/',
        string $domain = null,
        bool $secure = false,
        bool $httponly = true
    ): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_name($name);
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => 'Lax', // or 'Strict' or 'None'
            ]);

            session_start();
        }

        // Set session timeout
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $lifetime)) {
            self::destroy();
        }
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    public static function regenerate(): void
    {
        session_regenerate_id();
    }

    public static function set(string $key, string $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, string $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function flash(string $key, string $value = null)
    {
        if ($value) {
            self::set($key, $value);
            self::set($key . '_flash', true);
        } elseif (self::get($key . '_flash')) {
            $value = self::get($key);
            self::remove($key);
            self::remove($key . '_flash');
            return $value;
        }

        return null;
    }
}
