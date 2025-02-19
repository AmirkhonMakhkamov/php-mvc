<?php

namespace App\Core;

use App\Utilities\Logger;
use Exception;
use Monolog\Logger as MonologLogger;

abstract class Model {
    protected Database $db;
    protected MonologLogger $logger;

    public function __construct() {
        $this->logger = Logger::get();
        $this->db = Database::connect();
    }

    /**
     * Insert a record into the database.
     *
     * @param string $table
     * @param array $data
     * @throws Exception
     */
    protected function insert(string $table, array $data): void {
        try {
            $columns = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

            $this->db->prepare($sql);

            foreach ($data as $column => $value) {
                $this->db->bind(":$column", $value);
            }

            $this->db->execute();

            $this->logger->info(
                'Record inserted successfully.',
                ['table' => $table, 'data' => $data]
            );

        } catch (Exception $e) {
            $this->logger->error(
                'Failed to insert record.',
                ['table' => $table, 'data' => $data, 'message' => $e->getMessage()]
            );
            throw $e;
        }
    }

    /**
     * Select records from the database.
     *
     * @param string $table
     * @param array $columns
     * @param array $conditions
     * @param array $order
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws Exception
     */
    protected function select(
        string $table,
        array $columns,
        array $conditions = [],
        array $order = [],
        int $limit = 0,
        int $offset = 0
    ): array {
        try {
            $columns = implode(", ", $columns);
            $conditionPart = empty($conditions) ? "" : " WHERE " . implode(" AND ", array_map(fn($col) => "$col = :$col", array_keys($conditions)));
            $orderPart = empty($order) ? "" : " ORDER BY " . implode(", ", $order);
            $limitPart = $limit > 0 ? " LIMIT $limit" : "";
            $offsetPart = $offset > 0 ? " OFFSET $offset" : "";
            $sql = "SELECT $columns FROM $table$conditionPart$orderPart$limitPart$offsetPart";

            $this->db->prepare($sql);

            foreach ($conditions as $column => $value) {
                $this->db->bind(":$column", $value);
            }

            $this->db->execute();
            $result = $this->db->fetchAll();

            $this->logger->info(
                'Records selected successfully.',
                ['table' => $table, 'conditions' => $conditions, 'result' => $result]
            );

            return $result;

        } catch (Exception $e) {
            $this->logger->error(
                'Failed to select records.',
                ['table' => $table, 'conditions' => $conditions, 'message' => $e->getMessage()]
            );
            throw $e;
        }
    }

    /**
     * Update records in the database.
     *
     * @param string $table
     * @param array $data
     * @param array $conditions
     * @throws Exception
     */
    protected function update(string $table, array $data, array $conditions): void
    {
        try {
            $setPart = implode(", ", array_map(fn($col) => "$col = :$col", array_keys($data)));
            $conditionPart = implode(" AND ", array_map(fn($col) => "$col = :cond_$col", array_keys($conditions)));
            $sql = "UPDATE $table SET $setPart WHERE $conditionPart";

            $this->db->prepare($sql);

            foreach ($data as $column => $value) {
                $this->db->bind(":$column", $value);
            }

            foreach ($conditions as $column => $value) {
                $this->db->bind(":cond_$column", $value);
            }

            $this->db->execute();

            $this->logger->info(
                'Record updated successfully.',
                ['table' => $table, 'data' => $data, 'conditions' => $conditions]
            );

        } catch (Exception $e) {
            $this->logger->error(
                'Failed to update record.',
                ['table' => $table, 'data' => $data, 'conditions' => $conditions, 'message' => $e->getMessage()]
            );
            throw $e;
        }
    }

    /**
     * Delete records from the database.
     *
     * @param string $table
     * @param array $conditions
     * @throws Exception
     */
    protected function delete(string $table, array $conditions): void {
        try {
            $conditionPart = implode(" AND ", array_map(fn($col) => "$col = :cond_$col", array_keys($conditions)));
            $sql = "DELETE FROM $table WHERE $conditionPart";

            $this->db->prepare($sql);

            foreach ($conditions as $column => $value) {
                $this->db->bind(":cond_$column", $value);
            }

            $this->db->execute();

            $this->logger->info(
                'Record deleted successfully.',
                ['table' => $table, 'conditions' => $conditions]
            );

        } catch (Exception $e) {
            $this->logger->error(
                'Failed to delete record.',
                ['table' => $table, 'conditions' => $conditions, 'message' => $e->getMessage()]
            );
            throw $e;
        }
    }

    /**
     * Check if a record exists in the database.
     *
     * @param string $table
     * @param array $conditions
     * @return bool
     * @throws Exception
     */
    protected function exists(string $table, array $conditions): bool {
        try {
            $conditionPart = implode(" AND ", array_map(fn($col) => "$col = :$col", array_keys($conditions)));
            $sql = "SELECT COUNT(*) FROM $table WHERE $conditionPart";

            $this->db->prepare($sql);

            foreach ($conditions as $column => $value) {
                $this->db->bind(":$column", $value);
            }

            $this->db->execute();

            $count = $this->db->fetchColumn();

            $this->logger->info(
                'Existence check performed.',
                ['table' => $table, 'conditions' => $conditions, 'exists' => $count > 0]
            );

            return $count > 0;

        } catch (Exception $e) {
            $this->logger->error(
                'Failed to check existence.',
                ['table' => $table, 'conditions' => $conditions, 'message' => $e->getMessage()]
            );
            throw $e;
        }
    }

    /**
     * Count records in the database.
     *
     * @param string $table
     * @param array $conditions
     * @return int
     * @throws Exception
     */
    protected function count(string $table, array $conditions = []): int {
        try {
            $conditionPart = empty($conditions) ? "" : " WHERE " . implode(" AND ", array_map(fn($col) => "$col = :$col", array_keys($conditions)));
            $sql = "SELECT COUNT(*) FROM $table$conditionPart";

            $this->db->prepare($sql);

            foreach ($conditions as $column => $value) {
                $this->db->bind(":$column", $value);
            }

            $this->db->execute();

            $count = $this->db->fetchColumn();

            $this->logger->info(
                'Count performed.',
                ['table' => $table, 'conditions' => $conditions, 'count' => $count]
            );

            return $count;

        } catch (Exception $e) {
            $this->logger->error(
                'Failed to count records.',
                ['table' => $table, 'conditions' => $conditions, 'message' => $e->getMessage()]
            );
            throw $e;
        }
    }

    /**
     * Get the last inserted ID.
     *
     * @return string
     */
    public function lastInsertId(): string {
        return $this->db->lastInsertId();
    }

    /**
     * Get the number of rows affected by the last query.
     *
     * @return int
     */
    public function rowCount(): int {
        return $this->db->rowCount();
    }

    /**
     * Begin a database transaction.
     *
     * @return void
     */
    public function beginTransaction(): void {
        $this->db->beginTransaction();
    }

    /**
     * Commit the current transaction.
     *
     * @return void
     */
    public function commit(): void {
        $this->db->commit();
    }

    /**
     * Rollback the current transaction.
     *
     * @return void
     */
    public function rollback(): void {
        $this->db->rollback();
    }
}