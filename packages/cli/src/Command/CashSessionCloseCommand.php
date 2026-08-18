<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Domain\Entity\CashSession;
use Caisse\Domain\ValueObject\Money;
use Caisse\Infrastructure\Repository\SqliteCashSessionRepository;

class CashSessionCloseCommand extends Command
{
    public function execute(array $args): int
    {
        if (count($args) < 2) {
            echo "Usage: pos session:close <session_id> <amount> [notes]\n";
            return 1;
        }

        [$sessionId, $amount, $notes] = array_pad($args, 3, null);
        
        $repo = new SqliteCashSessionRepository($this->connection);
        $session = $repo->findById(\Caisse\Domain\ValueObject\Uuid::fromString($sessionId));
        
        if (!$session) {
            echo "Cash session not found\n";
            return 1;
        }
        
        if (!$session->isOpen()) {
            echo "Cash session is already closed\n";
            return 1;
        }
        
        $session->close(Money::of((int)$amount), $notes);
        $repo->save($session);
        
        echo "Cash session closed: {$session->getId()}\n";
        return 0;
    }

    public function getDescription(): string
    {
        return 'Close a cash session';
    }
}
