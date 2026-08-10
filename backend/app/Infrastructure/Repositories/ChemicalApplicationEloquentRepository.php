<?php

namespace App\Infrastructure\Repositories;

use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Models\ChemicalApplication;
use App\Infrastructure\Repositories\Concerns\ScopesByParentUser;

final class ChemicalApplicationEloquentRepository
{
    /** @use ScopesByParentUser<ChemicalApplication> */
    use ScopesByParentUser;

    public function existsInScope(UserId $userId, int $chemicalApplicationId): bool
    {
        return $this->scopeByParentUser(ChemicalApplication::query(), $userId)
            ->whereKey($chemicalApplicationId)
            ->exists();
    }

    protected function parentUserScope(): array
    {
        return ['parentTable' => 'work_logs', 'foreignKey' => 'work_log_id'];
    }
}
