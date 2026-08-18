<?php

declare(strict_types=1);

namespace Caisse\Sync\Queue;

use Caisse\Domain\Entity\SyncQueue;
use Caisse\Domain\Entity\SyncQueueStatus;
use Caisse\Infrastructure\Repository\SqliteSyncQueueRepository;

class SyncQueueService
{
    public function __construct(private SqliteSyncQueueRepository $repository)
    {
    }

    public function enqueue(SyncQueue $queue): void
    {
        $this->repository->save($queue);
    }

    /** @return array<int, SyncQueue> */
    public function getPending(int $limit = 100): array
    {
        return $this->repository->findPending($limit);
    }

    /** @return array<int, SyncQueue> */
    public function getFailed(int $limit = 100): array
    {
        return $this->repository->findFailed($limit);
    }

    public function markCompleted(SyncQueue $queue): void
    {
        $queue->markCompleted();
        $this->repository->save($queue);
    }

    public function markFailed(SyncQueue $queue, string $error): void
    {
        $queue->markFailed($error);
        $this->repository->save($queue);
    }

    public function retry(SyncQueue $queue): void
    {
        $queue->retry();
        $this->repository->save($queue);
    }
}
