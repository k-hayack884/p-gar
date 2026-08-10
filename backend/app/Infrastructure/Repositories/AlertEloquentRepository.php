<?php

namespace App\Infrastructure\Repositories;

use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Models\Alert;
use App\Infrastructure\Repositories\Concerns\ScopesByParentUser;

final class AlertEloquentRepository
{
    use ScopesByParentUser;

    public function existsInScope(UserId $userId, int $alertId): bool
    {
        return $this->scopeByParentUser(Alert::query(), $userId)
            ->whereKey($alertId)
            ->exists();
    }

    protected function parentUserScope(): array
    {
        return ['parentTable' => 'alert_rules', 'foreignKey' => 'alert_rule_id'];
    }
}
