<?php

namespace App\Utilities;

use Exception;
use SendGrid;
use SendGrid\Mail\Mail;

class Mailer
{
    /**
     * @throws Exception
     */
    public static function send(
        string $to, string $subject, string $body,
    ): void
    {
        $email = new Mail();
        $config = self::getConfig();

        try {
            $email->setFrom($config['address'], $config['name']);
            $email->setSubject($subject);
            $email->addTo($to);
            $email->addContent("text/plain", strip_tags($body));
            $email->addContent("text/html", $body);
        } catch (Exception $e) {
            throw new Exception('Error setting email: ' . $e->getMessage());
        }

        $sendgrid = new SendGrid(Env::get('SENDGRID_API_KEY'));

        try {
            $response = $sendgrid->send($email);

            if ($response->statusCode() !== 202) {
                throw new Exception('Error sending email: ' . $response->body());
            }

        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }

    private static function getConfig(): array {
        $config = require ROOT . '/app/config/config.php';
        return $config['mail'];
    }
}