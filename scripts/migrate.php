<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Caisse\Infrastructure\Database\Connection;
use Caisse\Infrastructure\Migration\MigrationRunner;

$databasePath = $argv[1] ?? 'database/caisse.db';
$connection = Connection::getInstance($databasePath);

$runner = new MigrationRunner($connection);
$runner->runMigrations(require __DIR__ . '/../packages/infrastructure/src/Migration/Migrations.php');

echo "Migrations completed successfully.\n";
