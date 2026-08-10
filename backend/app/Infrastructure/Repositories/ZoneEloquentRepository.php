<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\ZoneRepositoryInterface;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Models\Zone;
use App\Infrastructure\Repositories\Concerns\ScopesByOwnUser;

final class ZoneEloquentRepository implements ZoneRepositoryInterface
{
    use ScopesByOwnUser;

    public function existsInScope(UserId $userId, int $zoneId): bool
    {
        return $this->scopeByOwnUser(Zone::query(), $userId)
            ->whereKey($zoneId)
            ->exists();
    }
}
