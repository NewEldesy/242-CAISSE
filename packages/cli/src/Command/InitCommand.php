<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Infrastructure\Database\Connection;
use Caisse\Infrastructure\Migration\MigrationRunner;

class InitCommand extends Command
{
    public function __construct(Connection $connection, private string $databasePath = 'database/caisse.db')
    {
        parent::__construct($connection);
    }

    public function execute(array $args): int
    {
        $this->ensureDatabaseDirectory();
        
        $runner = new MigrationRunner($this->connection);
        $runner->runMigrations(require __DIR__ . '/../../../infrastructure/src/Migration/Migrations.php');
        
        echo "Database initialized successfully.\n";
        return 0;
    }

    public function getDescription(): string
    {
        return 'Initialize the SQLite database and run migrations';
    }

    private function ensureDatabaseDirectory(): void
    {
        $dir = dirname($this->databasePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
