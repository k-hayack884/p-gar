<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\PlantRepositoryInterface;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Models\Plant;
use App\Infrastructure\Repositories\Concerns\ScopesByOwnUser;

final class PlantEloquentRepository implements PlantRepositoryInterface
{
    /** @use ScopesByOwnUser<Plant> */
    use ScopesByOwnUser;

    public function existsInScope(UserId $userId, int $plantId): bool
    {
        return $this->scopeByOwnUser(Plant::query(), $userId)
            ->whereKey($plantId)
            ->exists();
    }
}
