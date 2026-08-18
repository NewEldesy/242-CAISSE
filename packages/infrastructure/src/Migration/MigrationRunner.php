<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Migration;

use Caisse\Infrastructure\Database\Connection;
use RuntimeException;

class MigrationRunner
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function runMigrations(array $migrations): void
    {
        $this->connection->beginTransaction();
        try {
            $this->createMigrationsTable();
            
            foreach ($migrations as $migration) {
                $this->runMigration($migration);
            }
            
            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollback();
            throw new RuntimeException("Migration failed: " . $e->getMessage());
        }
    }

    private function createMigrationsTable(): void
    {
        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration TEXT NOT NULL,
                executed_at TEXT NOT NULL
            )
        ");
    }

    private function runMigration(string $migration): void
    {
        $stmt = $this->connection->fetchOne(
            "SELECT id FROM migrations WHERE migration = ?",
            [$migration]
        );

        if ($stmt) {
            return;
        }

        $this->connection->execute($migration);
        $this->connection->execute(
            "INSERT INTO migrations (migration, executed_at) VALUES (?, ?)",
            [$migration, (new \DateTimeImmutable())->format('c')]
        );
    }
}
