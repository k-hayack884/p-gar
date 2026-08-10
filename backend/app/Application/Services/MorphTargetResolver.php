<?php

namespace App\Application\Services;

use App\Domain\Exceptions\InvalidMorphTypeException;
use App\Domain\Exceptions\NotFoundInScopeException;
use App\Domain\Repositories\PlantRepositoryInterface;
use App\Domain\Repositories\SiteRepositoryInterface;
use App\Domain\Repositories\WorkLogRepositoryInterface;
use App\Domain\Repositories\ZoneRepositoryInterface;
use App\Domain\ValueObjects\ImageableType;
use App\Domain\ValueObjects\MorphTarget;
use App\Domain\ValueObjects\UserId;

/**
 * Resolves a morph target before a use case begins its transaction.
 *
 * Use cases must persist morph columns only from the returned MorphTarget and
 * must never pass request arrays directly to Eloquent fill().
 */
final readonly class MorphTargetResolver
{
    public function __construct(
        private SiteRepositoryInterface $sites,
        private ZoneRepositoryInterface $zones,
        private PlantRepositoryInterface $plants,
        private WorkLogRepositoryInterface $workLogs,
    ) {}

    /**
     * @param  list<string>  $allowed  Morph aliases permitted by the caller.
     *
     * @throws InvalidMorphTypeException For an invalid type or identifier (HTTP 422).
     * @throws NotFoundInScopeException For missing or other-user targets (HTTP 404).
     */
    public function resolve(UserId $userId, string $type, int $id, array $allowed): MorphTarget
    {
        $knownTypes = array_map(
            static fn (ImageableType $imageableType): string => $imageableType->value,
            ImageableType::cases(),
        );

        if ($id < 1) {
            throw new InvalidMorphTypeException('Morph target ID must be a positive integer.');
        }

        if (! in_array($type, $knownTypes, true) || ! in_array($type, $allowed, true)) {
            throw new InvalidMorphTypeException("Morph type [{$type}] is not allowed.");
        }

        $exists = match ($type) {
            ImageableType::Site->value => $this->sites->existsInScope($userId, $id),
            ImageableType::Zone->value => $this->zones->existsInScope($userId, $id),
            ImageableType::Plant->value => $this->plants->existsInScope($userId, $id),
            ImageableType::WorkLog->value => $this->workLogs->existsInScope($userId, $id),
        };

        if (! $exists) {
            throw new NotFoundInScopeException('Morph target was not found in the current user scope.');
        }

        return new MorphTarget($type, $id);
    }
}
