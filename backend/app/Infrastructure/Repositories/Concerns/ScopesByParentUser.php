<?php

namespace App\Infrastructure\Repositories\Concerns;

use App\Domain\ValueObjects\UserId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
trait ScopesByParentUser
{
    /**
     * @return array{parentTable: string, foreignKey: string}
     */
    abstract protected function parentUserScope(): array;

    /** @param Builder<TModel> $query @return Builder<TModel> */
    protected function scopeByParentUser(Builder $query, UserId $userId): Builder
    {
        ['parentTable' => $parentTable, 'foreignKey' => $foreignKey] = $this->parentUserScope();
        $childTable = $query->getModel()->getTable();

        $query->select("{$childTable}.*");
        $query->join($parentTable, "{$parentTable}.id", '=', "{$childTable}.{$foreignKey}");

        return $query->where("{$parentTable}.user_id", $userId->value);
    }
}
