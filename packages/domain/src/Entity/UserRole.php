<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

enum UserRole: string
{
    case STORE_USER = 'store_user';
    case STORE_MANAGER = 'store_manager';
    case CENTRAL_ADMIN = 'central_admin';
}
