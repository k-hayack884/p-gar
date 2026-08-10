<?php

namespace Tests\Unit\Domain;

use App\Domain\ValueObjects\ImageableType;
use App\Domain\ValueObjects\TargetType;
use Illuminate\Database\Eloquent\Relations\Relation;
use Tests\TestCase;

class MorphTypeConsistencyTest extends TestCase
{
    public function test_target_types_are_a_subset_of_imageable_types(): void
    {
        $targetTypes = array_map(static fn (TargetType $type): string => $type->value, TargetType::cases());
        $imageableTypes = array_map(static fn (ImageableType $type): string => $type->value, ImageableType::cases());

        $this->assertEmpty(array_diff($targetTypes, $imageableTypes));
    }

    public function test_enforced_morph_map_has_exactly_the_domain_enum_values(): void
    {
        $mapKeys = array_keys(Relation::morphMap());
        $enumValues = array_unique(array_merge(
            array_map(static fn (TargetType $type): string => $type->value, TargetType::cases()),
            array_map(static fn (ImageableType $type): string => $type->value, ImageableType::cases()),
        ));

        sort($mapKeys);
        sort($enumValues);

        $this->assertSame($enumValues, $mapKeys);
    }
}
