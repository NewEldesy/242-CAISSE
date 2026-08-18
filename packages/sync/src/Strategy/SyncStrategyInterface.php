<?php

declare(strict_types=1);

namespace Caisse\Sync\Queue;

use Caisse\Domain\Entity\SyncQueue;

interface SyncStrategyInterface
{
    public function send(SyncQueue $queue): bool;
    public function getMaxRetries(): int;
}
