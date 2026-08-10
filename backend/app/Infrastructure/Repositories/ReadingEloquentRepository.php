<?php

namespace App\Infrastructure\Repositories;

use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Models\Reading;
use App\Infrastructure\Repositories\Concerns\ScopesByParentUser;

final class ReadingEloquentRepository
{
    /** @use ScopesByParentUser<Reading> */
    use ScopesByParentUser;

    public function existsInScope(UserId $userId, int $readingId): bool
    {
        return $this->scopeByParentUser(Reading::query(), $userId)
            ->whereKey($readingId)
            ->exists();
    }

    protected function parentUserScope(): array
    {
        return ['parentTable' => 'devices', 'foreignKey' => 'device_id'];
    }
}
