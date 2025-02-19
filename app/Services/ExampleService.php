<?php

namespace App\Services;

use App\Models\ExampleModel;
use Exception;

class ExampleService
{
    private ExampleModel $exampleModel;

    public function __construct(ExampleModel $exampleModel)
    {
        $this->exampleModel = $exampleModel;
    }

    /**
     * @throws Exception
     */
    public function insert(string $table, array $data): void
    {
        try {
            $this->exampleModel->beginTransaction();
            $this->exampleModel->exampleInsert($table, $data);
            $this->exampleModel->commit();
        } catch (Exception $e) {
            $this->exampleModel->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function insertMultiple(string $table, array $data): void
    {
        try {
            $this->exampleModel->beginTransaction();
            foreach ($data as $row) {
                $this->exampleModel->exampleInsert($table, $row);
            }
            $this->exampleModel->commit();
        } catch (Exception $e) {
            $this->exampleModel->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function select(
        string $table, array $columns, array $conditions = [], array $order = [], int $limit = 0, int $offset = 0
    ): array
    {
        return $this->exampleModel->exampleSelect(
            $table, $columns, $conditions, $order, $limit, $offset
        );
    }

    /**
     * @throws Exception
     */
    public function update(string $table, array $data, array $conditions): void
    {
        try {
            $this->exampleModel->beginTransaction();
            $this->exampleModel->exampleUpdate($table, $data, $conditions);
            $this->exampleModel->commit();
        } catch (Exception $e) {
            $this->exampleModel->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function delete(string $table, array $conditions): void
    {
        try {
            $this->exampleModel->beginTransaction();
            $this->exampleModel->exampleDelete($table, $conditions);
            $this->exampleModel->commit();
        } catch (Exception $e) {
            $this->exampleModel->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function exists(string $table, array $conditions): bool
    {
        return $this->exampleModel->exampleExists($table, $conditions);
    }

    /**
     * @throws Exception
     */
    public function count(string $table, array $conditions = []): int
    {
        return $this->exampleModel->exampleCount($table, $conditions);
    }
}