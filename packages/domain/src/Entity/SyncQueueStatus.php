<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

enum SyncQueueStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
