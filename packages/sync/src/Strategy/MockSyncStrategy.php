<?php

declare(strict_types=1);

namespace Caisse\Sync\Strategy;

use Caisse\Domain\Entity\SyncQueue;

class MockSyncStrategy implements SyncStrategyInterface
{
    public function send(SyncQueue $queue): bool
    {
        return true;
    }

    public function getMaxRetries(): int
    {
        return 3;
    }
}
