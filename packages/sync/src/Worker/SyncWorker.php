<?php

declare(strict_types=1);

namespace Caisse\Sync\Worker;

use Caisse\Domain\Entity\SyncQueue;
use Caisse\Sync\Queue\SyncQueueService;
use Caisse\Sync\Strategy\SyncStrategyInterface;

class SyncWorker
{
    public function __construct(
        private SyncQueueService $queueService,
        private SyncStrategyInterface $strategy,
        private int $batchSize = 10
    ) {
    }

    public function run(): void
    {
        $pending = $this->queueService->getPending($this->batchSize);
        
        foreach ($pending as $queue) {
            $this->process($queue);
        }
    }

    private function process(SyncQueue $queue): void
    {
        $queue->markProcessing();
        $this->queueService->enqueue($queue);
        
        try {
            $success = $this->strategy->send($queue);
            
            if ($success) {
                $this->queueService->markCompleted($queue);
            } else {
                $this->handleFailure($queue, 'Sync strategy returned false');
            }
        } catch (\Throwable $e) {
            $this->handleFailure($queue, $e->getMessage());
        }
    }

    private function handleFailure(SyncQueue $queue, string $error): void
    {
        if ($queue->getRetryCount() >= $this->strategy->getMaxRetries()) {
            $this->queueService->markFailed($queue, $error);
        } else {
            $this->queueService->retry($queue);
        }
    }
}
