<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

enum DeviceType: string
{
    case POS = 'pos';
    case MOBILE = 'mobile';
    case TABLET = 'tablet';
}
