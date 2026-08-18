<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Infrastructure\Database\Connection;

class SqliteSyncLogRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function log(string $eventId, string $status, ?int $httpStatus, ?string $responseBody, ?string $errorMessage): void
    {
        $this->connection->execute(
            "INSERT INTO sync_logs (id, event_id, status, http_status, response_body, error_message, executed_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                \Caisse\Domain\ValueObject\Uuid::generate()->getValue(),
                $eventId,
                $status,
                $httpStatus,
                $responseBody,
                $errorMessage,
                (new \DateTimeImmutable())->format('c'),
            ]
        );
    }
}
