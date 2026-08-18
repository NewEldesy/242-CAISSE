<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\Customer;
use Caisse\Domain\Entity\CustomerStatus;
use Caisse\Domain\Repository\CustomerRepositoryInterface;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteCustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(Customer $customer): void
    {
        $this->connection->execute(
            "INSERT OR REPLACE INTO customers (id, store_id, first_name, last_name, phone, email, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $customer->getId()->getValue(),
                $customer->getStoreId()->getValue(),
                $customer->getFirstName(),
                $customer->getLastName(),
                $customer->getPhone(),
                $customer->getEmail(),
                $customer->getStatus()->value,
                $customer->getCreatedAt()->format('c'),
                $customer->getUpdatedAt()->format('c'),
            ]
        );
    }

    public function findById(Uuid $id): ?Customer
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM customers WHERE id = ?",
            [$id->getValue()]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /** @return array<int, Customer> */
    public function findByStoreId(Uuid $storeId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM customers WHERE store_id = ?",
            [$storeId->getValue()]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function delete(Uuid $id): void
    {
        $this->connection->execute(
            "DELETE FROM customers WHERE id = ?",
            [$id->getValue()]
        );
    }

    private function hydrate(array $row): Customer
    {
        return Customer::reconstitute(
            Uuid::fromString($row['id']),
            Uuid::fromString($row['store_id']),
            $row['first_name'],
            $row['last_name'],
            $row['phone'],
            $row['email'],
            CustomerStatus::from($row['status']),
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at'])
        );
    }
}
