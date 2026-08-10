<?php

namespace Tests\Unit\Application;

use App\Application\Services\MorphTargetResolver;
use App\Domain\Exceptions\InvalidMorphTypeException;
use App\Domain\Exceptions\NotFoundInScopeException;
use App\Domain\Repositories\PlantRepositoryInterface;
use App\Domain\Repositories\SiteRepositoryInterface;
use App\Domain\Repositories\WorkLogRepositoryInterface;
use App\Domain\Repositories\ZoneRepositoryInterface;
use App\Domain\ValueObjects\ImageableType;
use App\Domain\ValueObjects\TargetType;
use App\Domain\ValueObjects\UserId;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MorphTargetResolverTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    #[DataProvider('existingTargetProvider')]
    public function test_it_resolves_each_owned_target(string $type, string $repository): void
    {
        $repositories = $this->repositoriesWithResult($repository, true);
        $resolver = new MorphTargetResolver(...$repositories);

        $target = $resolver->resolve(new UserId(7), $type, 42, $this->imageableTypes());

        $this->assertSame($type, $target->type);
        $this->assertSame(42, $target->id);
    }

    #[DataProvider('existingTargetProvider')]
    public function test_it_hides_other_user_or_missing_targets(string $type, string $repository): void
    {
        $repositories = $this->repositoriesWithResult($repository, false);
        $resolver = new MorphTargetResolver(...$repositories);

        $this->expectException(NotFoundInScopeException::class);

        $resolver->resolve(new UserId(7), $type, 42, $this->imageableTypes());
    }

    public function test_it_rejects_unknown_disallowed_and_non_positive_morph_values(): void
    {
        $resolver = new MorphTargetResolver(
            Mockery::mock(SiteRepositoryInterface::class),
            Mockery::mock(ZoneRepositoryInterface::class),
            Mockery::mock(PlantRepositoryInterface::class),
            Mockery::mock(WorkLogRepositoryInterface::class),
        );

        foreach ([
            ['device', 1, $this->imageableTypes()],
            [ImageableType::WorkLog->value, 1, $this->targetTypes()],
            [ImageableType::Site->value, 0, $this->imageableTypes()],
            [ImageableType::Site->value, -1, $this->imageableTypes()],
        ] as [$type, $id, $allowed]) {
            try {
                $resolver->resolve(new UserId(7), $type, $id, $allowed);
                $this->fail('Invalid morph data must not resolve.');
            } catch (InvalidMorphTypeException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public static function existingTargetProvider(): array
    {
        return [
            'site' => [ImageableType::Site->value, 'sites'],
            'zone' => [ImageableType::Zone->value, 'zones'],
            'plant' => [ImageableType::Plant->value, 'plants'],
            'work log' => [ImageableType::WorkLog->value, 'workLogs'],
        ];
    }

    /** @return array{SiteRepositoryInterface, ZoneRepositoryInterface, PlantRepositoryInterface, WorkLogRepositoryInterface} */
    private function repositoriesWithResult(string $selectedRepository, bool $result): array
    {
        $repositories = [
            'sites' => Mockery::mock(SiteRepositoryInterface::class),
            'zones' => Mockery::mock(ZoneRepositoryInterface::class),
            'plants' => Mockery::mock(PlantRepositoryInterface::class),
            'workLogs' => Mockery::mock(WorkLogRepositoryInterface::class),
        ];

        $repositories[$selectedRepository]
            ->shouldReceive('existsInScope')
            ->once()
            ->with(Mockery::type(UserId::class), 42)
            ->andReturn($result);

        return array_values($repositories);
    }

    /** @return list<string> */
    private function imageableTypes(): array
    {
        return array_map(static fn (ImageableType $type): string => $type->value, ImageableType::cases());
    }

    /** @return list<string> */
    private function targetTypes(): array
    {
        return array_map(static fn (TargetType $type): string => $type->value, TargetType::cases());
    }
}
