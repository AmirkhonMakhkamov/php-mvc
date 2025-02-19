<?php

namespace App\Models;

use App\Core\Model;
use Exception;

class ExampleModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function exampleInsert(string $table, array $data): void
    {
        $this->insert($table, $data);
    }

    /**
     * @throws Exception
     */
    public function exampleSelect(
        string $table, array $columns, array $conditions = [], array $order = [], int $limit = 0, int $offset = 0
    ): array
    {
        return $this->select(
            $table, $columns, $conditions, $order, $limit, $offset
        );
    }

    /**
     * @throws Exception
     */
    public function exampleUpdate(string $table, array $data, array $conditions): void
    {
        $this->update($table, $data, $conditions);
    }

    /**
     * @throws Exception
     */
    public function exampleDelete(string $table, array $conditions): void
    {
        $this->delete($table, $conditions);
    }

    /**
     * @throws Exception
     */
    public function exampleExists(string $table, array $conditions): bool
    {
        return $this->exists($table, $conditions);
    }

    /**
     * @throws Exception
     */
    public function exampleCount(string $table, array $conditions = []): int
    {
        return $this->count($table, $conditions);
    }
}