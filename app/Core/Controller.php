<?php

namespace App\Core;

use App\Utilities\Env;
use App\Utilities\Session;
use App\Utilities\Token;
use JetBrains\PhpStorm\NoReturn;

abstract class Controller
{
    #[NoReturn] protected function redirect(string $path): void
    {
        header("Location: $path");
        exit();
    }

    protected function sanitizeString(
        string $string,
        int $flags = ENT_QUOTES | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false
    ): string
    {
        return htmlspecialchars($string, $flags, $encoding, $doubleEncode);
    }

    protected function filterUrl(string $url): ?string
    {
        return filter_var(trim($url), FILTER_VALIDATE_URL);
    }

    protected function filterEmail(string $email): ?string
    {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    }

    protected function filterInt(int $int): ?int
    {
        return filter_var(trim($int), FILTER_VALIDATE_INT);
    }

    protected function filterFloat(float $float): ?float
    {
        return filter_var(trim($float), FILTER_VALIDATE_FLOAT);
    }

    protected function isUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    protected function sanitizeArray(array $array): array
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->sanitizeArray($value);
            } elseif ($this->isUrl($value)) {
                $value = $this->filterUrl($value);
            } elseif (is_int($value)) {
                $value = $this->filterInt($value);
            } else {
                $value = $this->sanitizeString($value);
            }
        }
        return $array;
    }

    protected function checkArrayKeys(array $array, array $keys): bool
    {
        foreach ($array as $key => $value) {
            if (!in_array($key, $keys)) {
                return false;
            }
        }
        return true;
    }

    protected function checkArrayValues(array $array, array $values): bool
    {
        foreach ($values as $value) {
            if (empty($array[$value])) {
                return false;
            }
        }

        return true;
    }

    protected function checkArray(array $array, array $keys): bool
    {
        if (!$this->checkArrayKeys($array, $keys)) {
            return false;
        }

        if (!$this->checkArrayValues($array, $keys)) {
            return false;
        }

        return true;
    }

    protected function getBody() : array
    {
        $body = file_get_contents('php://input');
        return json_decode($body, true);
    }

    protected function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    #[NoReturn] protected function jsonResponse($data, $statusCode = 200): void
    {
        // header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    protected function verifyMethod(string $method): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            return false;
        }
        return true;
    }

    protected function validateXHR(): bool
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== strtolower(Env::get('XHR_HEADER'))) {
            return false;
        }
        return true;
    }

    protected function validateCSRF(): bool
    {
        $headers = isset(getallheaders()['X-CSRF-Token']) ? getallheaders()['X-CSRF-Token'] : null;

        if ($headers && Token::verify('CSRF_TOKEN', $headers)) {
            return true;
        }

        return false;
    }

    private function setSecurityHeaders(): void
    {
        header("Content-Security-Policy: default-src 'self'; script-src 'self'");
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: SAMEORIGIN");
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        header("X-XSS-Protection: 1; mode=block");
    }

    protected function fetchRequest(string $method, string $type): void
    {
        $this->setSecurityHeaders();

        match ($type) {
            'json' => header('Content-Type: application/json'),
            default => header('Content-Type: text/html')
        };

        if (!$this->verifyMethod($method)) {
            $this->jsonResponse(
                ['success' => false, 'message' => 'Invalid request method.'],
                400
            );
        }

        if (!$this->validateXHR()) {
            $this->jsonResponse(
                ['success' => false, 'message' => 'XHR validation failed.'],
                400
            );
        }

        if (!$this->validateCsrf()) {
            $this->jsonResponse(
                ['success' => false, 'message' => 'CSRF validation failed.'],
                400
            );
        }
    }
}