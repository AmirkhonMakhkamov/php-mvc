<?php

namespace App\Core;

use Exception;
use PDO;
use PDOException;
use PDOStatement;
use App\Utilities\Logger;
use Monolog\Logger as MonologLogger;

class Database {
    private static ?Database $instance = null;
    private ?PDO $pdo;
    private ?PDOStatement $stmt;
    private MonologLogger $logger;

    /**
     * Prevent direct object creation.
     *
     * @throws Exception
     */
    private function __construct() {
        $this->logger = Logger::get('Core/Database');

        $config = require ROOT . '/app/config/database.php';
        $config = $config['connections'][$config['default']];

        // Validate configuration
        if (!isset($config['host'], $config['dbname'], $config['username'], $config['password'])) {
            $this->logger->error('Database configuration is incomplete');
            throw new Exception('Database configuration is incomplete');
        }

        try {
            $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'];
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true,
            ];
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $options
            );

            $this->logger->info('Database connection established.', ['dsn' => $dsn]);

        } catch (PDOException $e) {
            $error = $e->getMessage();
            $this->logger->error('Database connection failed.', ['error' => $error]);
            throw new Exception($error);
        }
    }

    // Prevent cloning and un-serialization of the singleton instance.
    public function __clone() {}
    public function __wakeup() {}

    /**
     * Get the singleton instance of the Database.
     *
     * @return Database
     */
    public static function connect(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Prepare a SQL query.
     *
     * @param string $sql
     * @throws Exception
     */
    public function prepare(string $sql): void {
        try {
            $this->logger->info('Preparing query.', ['sql' => $sql]);
            $this->stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            $error = $e->getMessage();
            $this->logger->error('Prepare query failed.', ['error' => $error]);
            throw new Exception($error);
        }
    }

    /**
     * Bind parameters to the SQL query.
     *
     * @param string $param
     * @param mixed $value
     * @param int|null $type
     * @return void
     */
    public function bind(string $param, mixed $value, ?int $type = null): void {
        if (is_null($type)) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
        }
        $this->logger->info('Binding parameter.', ['param' => $param, 'value' => $value, 'type' => $type]);
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Execute the prepared statement.
     *
     * @throws Exception
     * @return void
     */
    public function execute(): void {
        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $error = $e->getMessage();
            $this->logger->error('Executing query failed.', ['error' => $error]);
            throw new Exception($error);
        }
    }

    /**
     * Fetch all results as an array.
     *
     * @return array
     */
    public function fetchAll(): array {
        return $this->stmt->fetchAll();
    }

    /**
     * Fetch a single result as an array.
     *
     * @return ?array
     */
    public function fetch(): ?array {
        return $this->stmt->fetch() ?: null;
    }

    /**
     * Get the row count of the result.
     *
     * @return int
     */
    public function rowCount(): int {
        return $this->stmt->rowCount();
    }

    /**
     * Get the column count of the result.
     *
     * @return int
     */
    public function columnCount(): int {
        return $this->stmt->columnCount();
    }

    /**
     * Fetch a single column from the result.
     *
     * @return mixed
     */
    public function fetchColumn(): mixed {
        return $this->stmt->fetchColumn();
    }

    /**
     * Get the last inserted ID.
     *
     * @return string
     */
    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }

    /**
     * Begin a database transaction.
     *
     * @return bool
     */
    public function beginTransaction(): bool {
        $this->logger->info('Beginning transaction.');
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a database transaction.
     *
     * @return bool
     */
    public function commit(): bool {
        $this->logger->info('Committing transaction.');
        return $this->pdo->commit();
    }

    /**
     * Rollback a database transaction.
     *
     * @return bool
     */
    public function rollBack(): bool {
        $this->logger->info('Rolling back transaction.');
        return $this->pdo->rollBack();
    }

    /**
     * Execute a transaction.
     *
     * @param callable $transaction
     * @throws Exception
     * @return bool
     */
    public function executeTransaction(callable $transaction): bool {
        try {
            $this->beginTransaction();
            $transaction($this);
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            $this->logger->error('Transaction failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Dump debugging information about the prepared statement.
     *
     * @return void
     */
    public function debugDumpParams(): void {
        $this->stmt->debugDumpParams();
    }

    /**
     * Destructor to close the PDO connection.
     */
    public function __destruct() {
        $this->stmt = null;
        $this->pdo = null;
        $this->logger->info('Database connection closed.');
    }
}