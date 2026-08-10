<?php

namespace App\Domain\Repositories;

use App\Domain\ValueObjects\UserId;

interface WorkLogRepositoryInterface
{
    public function existsInScope(UserId $userId, int $workLogId): bool;
}
