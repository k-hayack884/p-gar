<?php

namespace App\Domain\Repositories;

use App\Domain\ValueObjects\UserId;

interface PlantRepositoryInterface
{
    public function existsInScope(UserId $userId, int $plantId): bool;
}
