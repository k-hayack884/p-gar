<?php

namespace App\Domain\Repositories;

use App\Domain\ValueObjects\UserId;

interface ZoneRepositoryInterface
{
    public function existsInScope(UserId $userId, int $zoneId): bool;
}
