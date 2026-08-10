<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\WorkLogRepositoryInterface;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Models\WorkLog;
use App\Infrastructure\Repositories\Concerns\ScopesByOwnUser;

final class WorkLogEloquentRepository implements WorkLogRepositoryInterface
{
    use ScopesByOwnUser;

    public function existsInScope(UserId $userId, int $workLogId): bool
    {
        return $this->scopeByOwnUser(WorkLog::query(), $userId)
            ->whereKey($workLogId)
            ->exists();
    }
}
