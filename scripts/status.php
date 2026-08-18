<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Caisse\Infrastructure\Database\Connection;

$databasePath = 'database/caisse.db';
$connection = Connection::getInstance($databasePath);

$tables = $connection->fetchAll("SELECT name FROM sqlite_master WHERE type='table'");

echo "Database: {$databasePath}\n\n";
echo "Tables:\n";
foreach ($tables as $table) {
    echo "  - " . $table['name'] . "\n";
    
    $count = $connection->fetchOne("SELECT COUNT(*) as cnt FROM " . $table['name']);
    echo "    Rows: " . ($count['cnt'] ?? 0) . "\n";
}
