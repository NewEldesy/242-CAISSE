<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Domain\Entity\CashSession;
use Caisse\Domain\ValueObject\Money;
use Caisse\Infrastructure\Repository\SqliteCashSessionRepository;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;
use Caisse\Infrastructure\Repository\SqliteDeviceRepository;
use Caisse\Infrastructure\Repository\SqliteUserRepository;

class CashSessionOpenCommand extends Command
{
    public function execute(array $args): int
    {
        if (count($args) < 4) {
            echo "Usage: pos session:open <store_code> <device_id> <user_id> <amount> [notes]\n";
            return 1;
        }

        [$storeCode, $deviceId, $userId, $amount, $notes] = array_pad($args, 5, null);
        
        $storeRepo = new SqliteStoreRepository($this->connection);
        $store = $storeRepo->findByCode($storeCode);
        
        if (!$store) {
            echo "Store not found: {$storeCode}\n";
            return 1;
        }

        $device = (new SqliteDeviceRepository($this->connection))->findById(\Caisse\Domain\ValueObject\Uuid::fromString($deviceId));
        $user = (new SqliteUserRepository($this->connection))->findById(\Caisse\Domain\ValueObject\Uuid::fromString($userId));
        
        if (!$device || !$user) {
            echo "Device or user not found\n";
            return 1;
        }
        
        $sessionRepo = new SqliteCashSessionRepository($this->connection);
        $existing = $sessionRepo->findOpenByDeviceAndUser($device->getId(), $user->getId());
        
        if ($existing) {
            echo "Cash session already open for this device/user\n";
            return 1;
        }
        
        $session = CashSession::open($store->getId(), $device->getId(), $user->getId(), Money::of((int)$amount), $notes);
        $sessionRepo->save($session);
        
        echo "Cash session opened: {$session->getId()}\n";
        return 0;
    }

    public function getDescription(): string
    {
        return 'Open a new cash session';
    }
}
