<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

enum ProductStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
