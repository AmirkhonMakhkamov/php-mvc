<?php

namespace App\Utilities;

use Random\RandomException;

class Token
{
    /**
     * Generate a secure token using a seed and random bytes.
     *
     * @return string
     */
    public static function generate(): string
    {
        try {
            // Generate a token using a secure random bytes method
            return hash(
                'sha256',
                Env::get('TOKEN_SEED') . bin2hex(random_bytes(32))
            );
        } catch (RandomException $e) {
            // Fallback in case random_bytes fails
            return hash(
                'sha256',
                Env::get('TOKEN_SEED') . uniqid(true)
            );
        }
    }

    /**
     * Set a token in the session if it does not already exist.
     *
     * @param string $token
     */
    public static function set(string $token): void
    {
        if (empty(Session::get($token))) {
            Session::set($token, self::generate());
        }
    }

    /**
     * Verify if the provided token matches the session token.
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public static function verify(string $key, string $value): bool
    {
        return hash_equals(
            Session::get($key),
            $value
        );
    }
}