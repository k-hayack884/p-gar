<?php

namespace App\Infrastructure\Repositories\Concerns;

use App\Domain\ValueObjects\UserId;
use Illuminate\Database\Eloquent\Builder;

trait ScopesByOwnUser
{
    /** @template TModel of \Illuminate\Database\Eloquent\Model */
    /** @param Builder<TModel> $query @return Builder<TModel> */
    protected function scopeByOwnUser(Builder $query, UserId $userId): Builder
    {
        return $query->where('user_id', $userId->value);
    }
}
