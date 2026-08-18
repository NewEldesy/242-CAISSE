<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Domain\Entity\Store;
use Caisse\Domain\Entity\User;
use Caisse\Domain\Entity\UserRole;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;
use Caisse\Infrastructure\Repository\SqliteUserRepository;

class UserCreateCommand extends Command
{
    public function execute(array $args): int
    {
        if (count($args) < 4) {
            echo "Usage: pos user:create <store_code> <username> <email> <password> [role] [full_name]\n";
            return 1;
        }

        [$storeCode, $username, $email, $password, $role, $fullName] = array_pad($args, 6, null);
        
        $storeRepo = new SqliteStoreRepository($this->connection);
        $store = $storeRepo->findByCode($storeCode);
        
        if (!$store) {
            echo "Store not found: {$storeCode}\n";
            return 1;
        }

        $userRole = UserRole::tryFrom($role ?? UserRole::STORE_USER->value) ?? UserRole::STORE_USER;
        $user = User::create($store->getId(), $username, $email, password_hash($password, PASSWORD_DEFAULT), $userRole, $fullName);
        
        $userRepo = new SqliteUserRepository($this->connection);
        $userRepo->save($user);
        
        echo "User created: {$user->getUsername()} (ID: {$user->getId()})\n";
        return 0;
    }

    public function getDescription(): string
    {
        return 'Create a new user in a store';
    }
}
