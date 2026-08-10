<?php

namespace App\Domain\Repositories;

use App\Domain\ValueObjects\UserId;

interface SiteRepositoryInterface
{
    public function existsInScope(UserId $userId, int $siteId): bool;
}
