<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Infrastructure\Database\Connection;
use Caisse\Infrastructure\Migration\MigrationRunner;

abstract class Command
{
    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    abstract public function execute(array $args): int;
    abstract public function getDescription(): string;
}
