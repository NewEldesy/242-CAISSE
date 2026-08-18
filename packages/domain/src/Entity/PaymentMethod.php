<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case MOBILE_MONEY = 'mobile_money';
    case CARD = 'card';
    case CHECK = 'check';
    case OTHER = 'other';
}
