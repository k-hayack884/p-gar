<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\SiteRepositoryInterface;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Models\Site;
use App\Infrastructure\Repositories\Concerns\ScopesByOwnUser;

final class SiteEloquentRepository implements SiteRepositoryInterface
{
    /** @use ScopesByOwnUser<Site> */
    use ScopesByOwnUser;

    public function existsInScope(UserId $userId, int $siteId): bool
    {
        return $this->scopeByOwnUser(Site::query(), $userId)
            ->whereKey($siteId)
            ->exists();
    }
}
