<?php

namespace App\Infrastructure\Repositories\Concerns;

use App\Domain\ValueObjects\UserId;
use Illuminate\Database\Eloquent\Builder;

trait ScopesByParentUser
{
    /**
     * @return array{parentTable: string, foreignKey: string}
     */
    abstract protected function parentUserScope(): array;

    /** @template TModel of \Illuminate\Database\Eloquent\Model */
    /** @param Builder<TModel> $query @return Builder<TModel> */
    protected function scopeByParentUser(Builder $query, UserId $userId): Builder
    {
        ['parentTable' => $parentTable, 'foreignKey' => $foreignKey] = $this->parentUserScope();
        $childTable = $query->getModel()->getTable();

        return $query
            ->select("{$childTable}.*")
            ->join($parentTable, "{$parentTable}.id", '=', "{$childTable}.{$foreignKey}")
            ->where("{$parentTable}.user_id", $userId->value);
    }
}
