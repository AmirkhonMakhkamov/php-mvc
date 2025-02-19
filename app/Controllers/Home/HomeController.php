<?php

namespace App\Controllers\Home;

use App\Core\Controller;
use App\Core\View;
use App\Utilities\Logger;
use Monolog\Logger as MonologLogger;

class HomeController extends Controller
{
    public MonologLogger $logger;

    public function __construct()
    {
        $this->logger = Logger::get();
    }

    public function index(array $param): void
    {
        View::render('Home/pages/index', []);
    }
}