<?php

namespace App\Core;

use App\Utilities\Cookie;
use App\Utilities\Env;
use App\Utilities\Error;
use App\Utilities\Session;
use App\Utilities\Token;
use App\Utilities\Logger;
use Monolog\Logger as MonologLogger;
use Exception;

class App
{
    private Router $router;
    private Request $request;
    private Response $response;
    private MonologLogger $logger;
    private array $config;

    public function __construct(Router $router, Request $request, Response $response)
    {
        $this->router = $router;
        $this->request = $request;
        $this->response = $response;
        $this->logger = Logger::get();
    }

    private function loadEnv(): void
    {
        Env::load();
    }

    private function loadConfig(): void
    {
        $this->config = require ROOT . '/app/config/config.php';
    }

    private function setTimezone(): void
    {
        $timezone = $this->config['app']['timezone'] ?? 'UTC';
        date_default_timezone_set($timezone);

        Cookie::set('APP_TIMEZONE', $timezone);
    }

    private function initErrorReporting(): void
    {
        if ($this->config['app']['debug']) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(0);
        }
    }

    private function startSession(): void
    {
        $sessionSettings = $this->config['session'] ?? [];
        Session::start(
            $sessionSettings['name'] ?? 'PHPSESSID',
            $sessionSettings['lifetime'] ?? 0,
            $sessionSettings['path'] ?? '/',
            $sessionSettings['domain'] ?? '',
            $sessionSettings['secure'] ?? false,
            $sessionSettings['httponly'] ?? false
        );
    }

    private function setTokens(): void
    {
        Token::set('TOKEN');
        Token::set('CSRF_TOKEN');
    }

    public function init(): void
    {
        $this->loadEnv();
        $this->loadConfig();
        $this->setTimezone();
        $this->initErrorReporting();
        $this->startSession();
        $this->setTokens();
    }

    public function run(): void
    {
        try {
            $this->init();
            $this->router->dispatch(
                $this->request, $this->response
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            Error::throw(500);
        }
    }
}