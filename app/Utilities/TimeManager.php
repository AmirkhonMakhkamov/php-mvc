<?php

namespace App\Utilities;

use DateTime;
use DateTimeZone;
use Exception;

class TimeManager
{
    /**
     * Convert date from app timezone to user's timezone and format it
     *
     * @param string $date
     * @param string $input
     * @param string $output
     * @return string
     */
    public static function convert(
        string $date, string $input = 'Y-m-d H:i:s', string $output = 'F j, Y g:i A'
    ): string
    {
        $appTimezone = Cookie::get('APP_TIMEZONE', 'UTC');
        $userTimezone = Cookie::get('USER_TIMEZONE', 'UTC');

        try {
            $dateTime = DateTime::createFromFormat($input, $date, new DateTimeZone($appTimezone));
            $dateTime->setTimezone(new DateTimeZone($userTimezone));
        } catch (Exception) {
            return 'Invalid date';
        }
        return $dateTime->format($output);
    }
}