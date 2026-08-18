<?php

declare(strict_types=1);

namespace Caisse\Cli;

use Caisse\Infrastructure\Database\Connection;

class Application
{
    private array $commands = [];

    public function __construct(
        private string $name,
        private string $version,
        private Connection $connection,
        private string $databasePath = 'database/caisse.db'
    ) {
        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        $this->commands['init'] = new Command\InitCommand($this->connection, $this->databasePath);
        $this->commands['store:create'] = new Command\StoreCreateCommand($this->connection);
        $this->commands['user:create'] = new Command\UserCreateCommand($this->connection);
        $this->commands['device:create'] = new Command\DeviceCreateCommand($this->connection);
        $this->commands['device:list'] = new Command\DeviceListCommand($this->connection);
        $this->commands['product:create'] = new Command\ProductCreateCommand($this->connection);
        $this->commands['product:list'] = new Command\ProductListCommand($this->connection);
        $this->commands['sale:create'] = new Command\SaleCreateCommand($this->connection);
        $this->commands['sale:list'] = new Command\SaleListCommand($this->connection);
        $this->commands['stock:list'] = new Command\StockListCommand($this->connection);
        $this->commands['session:open'] = new Command\CashSessionOpenCommand($this->connection);
        $this->commands['session:close'] = new Command\CashSessionCloseCommand($this->connection);
    }

    public function run(array $argv): int
    {
        $commandName = $argv[1] ?? 'help';
        
        if (!isset($this->commands[$commandName])) {
            $this->showHelp();
            return 1;
        }

        if ($commandName !== 'init') {
            $this->ensureDatabaseInitialized();
        }
        
        $command = $this->commands[$commandName];
        array_shift($argv);
        array_shift($argv);
        
        return $command->execute($argv);
    }

    private function ensureDatabaseInitialized(): void
    {
        $stmt = $this->connection->fetchOne(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='stores'"
        );
        
        if (!$stmt) {
            echo "Error: database not initialized.\n";
            echo "Run: php packages/cli/bin/pos init";
            if ($this->databasePath !== 'database/caisse.db') {
                echo " " . escapeshellarg($this->databasePath);
            }
            echo "\n";
            exit(1);
        }
    }

    private function showHelp(): void
    {
        echo "{$this->name} v{$this->version}\n\n";
        echo "Usage: pos <command> [options]\n\n";
        echo "Available commands:\n";
        foreach ($this->commands as $name => $command) {
            echo "  {$name}    {$command->getDescription()}\n";
        }
    }
}
