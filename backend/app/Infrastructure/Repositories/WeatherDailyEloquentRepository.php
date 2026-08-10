<?php

namespace App\Infrastructure\Repositories;

use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Models\WeatherDaily;
use App\Infrastructure\Repositories\Concerns\ScopesByParentUser;

final class WeatherDailyEloquentRepository
{
    /** @use ScopesByParentUser<WeatherDaily> */
    use ScopesByParentUser;

    /**
     * Weather dates are Asia/Tokyo calendar dates. Never convert them through UTC.
     */
    public function existsInScope(UserId $userId, int $weatherDailyId): bool
    {
        return $this->scopeByParentUser(WeatherDaily::query(), $userId)
            ->whereKey($weatherDailyId)
            ->exists();
    }

    protected function parentUserScope(): array
    {
        return ['parentTable' => 'sites', 'foreignKey' => 'site_id'];
    }
}
