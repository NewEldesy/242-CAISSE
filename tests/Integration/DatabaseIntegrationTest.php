<?php

declare(strict_types=1);

namespace Caisse\Tests\Integration;

use Caisse\Domain\Entity\Store;
use Caisse\Infrastructure\Database\Connection;
use Caisse\Infrastructure\Migration\MigrationRunner;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;
use PHPUnit\Framework\TestCase;

class DatabaseIntegrationTest extends TestCase
{
    private Connection $connection;
    private string $dbPath;

    protected function setUp(): void
    {
        $this->dbPath = sys_get_temp_dir() . '/caisse_test_' . uniqid() . '.db';
        $this->connection = Connection::getInstance($this->dbPath);
        
        $runner = new MigrationRunner($this->connection);
        $runner->runMigrations(require __DIR__ . '/../../../infrastructure/src/Migration/Migrations.php');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    public function testStoreCanBePersistedAndRetrieved(): void
    {
        $store = Store::create('Test Store', 'TEST001');
        $repo = new SqliteStoreRepository($this->connection);
        $repo->save($store);
        
        $found = $repo->findById($store->getId());
        $this->assertNotNull($found);
        $this->assertEquals('Test Store', $found->getName());
        $this->assertEquals('TEST001', $found->getCode());
    }

    public function testStoreCanBeFoundByCode(): void
    {
        $store = Store::create('Test Store', 'TEST002');
        $repo = new SqliteStoreRepository($this->connection);
        $repo->save($store);
        
        $found = $repo->findByCode('TEST002');
        $this->assertNotNull($found);
        $this->assertEquals($store->getId()->getValue(), $found->getId()->getValue());
    }

    public function testMultipleStoresCanBeListed(): void
    {
        $repo = new SqliteStoreRepository($this->connection);
        $repo->save(Store::create('Store A', 'A001'));
        $repo->save(Store::create('Store B', 'B001'));
        
        $all = $repo->findAll();
        $this->assertCount(2, $all);
    }
}
