<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Services\ExampleService;
use App\Utilities\Logger;
use Exception;
use Monolog\Logger as MonologLogger;

class ExampleController extends Controller
{
    private MonologLogger $logger;
    private ExampleService $exampleService;

    public function __construct(ExampleService $exampleService)
    {
        $this->logger = Logger::get();
        $this->exampleService = $exampleService;
    }

    public function index(array $param): void
    {
        $this->fetchRequest('POST', 'json');
        View::render('Home/pages/index', []);
    }

    public function session(array $param): void
    {
        echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';
    }
    public function insert(array $param): void
    {
        $table = 'home';
        $data = [
            'name' => 'John Doe',
            'description' => 'This is a test description',
        ];

        try {
            $this->exampleService->insert($table, $data);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            echo $error;
        }
    }

    public function insertMultiple(array $param): void
    {
        $table = 'home';
        $data = [
            [
                'name' => 'John Doe 1',
                'description' => 'This is a test description 1',
            ],
            [
                'name' => 'John Doe 2',
                'description' => 'This is a test description 2',
            ],
            [
                'name' => 'John Doe 3',
                'description' => 'This is a test description 3',
            ],
        ];

        try {
            $this->exampleService->insertMultiple($table, $data);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            echo $error;
        }
    }

    public function select(array $param): void
    {
        $table = 'home';
        $columns = ['id', 'name', 'description'];
        $conditions = [
            'id' => 3,
        ];
        $order = [
            'name ASC',
        ];
        $limit = 1;
        $offset = 0;

        try {
            $result = $this->exampleService->select(
                $table, $columns, $conditions, $order, $limit, $offset
            );

            echo '<pre>';
            print_r($result);
            echo '</pre>';

        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            echo $error;
        }
    }

    public function update(array $param): void
    {
        $table = 'home';
        $data = [
            'name' => 'John Doe Updated',
            'description' => 'This is a test description updated',
        ];
        $conditions = [
            'id' => 1,
            'name' => 'John Doe',
        ];

        try {
            $this->exampleService->update($table, $data, $conditions);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            echo $error;
        }
    }

    public function delete(array $param): void
    {
        $table = 'home';
        $conditions = [
            'id' => 1,
            'name' => 'John Doe Updated',
        ];

        try {
            $this->exampleService->delete($table, $conditions);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            echo $error;
        }
    }

    public function count(array $param): void
    {
        $table = 'home';
        $conditions = [
            'id' => 1,
        ];

        try {
            $result = $this->exampleService->count($table, $conditions);
            echo $result;
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            echo $error;
        }
    }

    public function exists(array $param): void
    {
        $table = 'home';
        $conditions = [
            'id' => 2,
        ];

        try {
            $result = $this->exampleService->exists($table, $conditions);
            var_dump($result);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
            echo $error;
        }
    }
}