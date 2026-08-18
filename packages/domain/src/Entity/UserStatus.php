<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case LOCKED = 'locked';
}
