<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

enum CustomerStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
