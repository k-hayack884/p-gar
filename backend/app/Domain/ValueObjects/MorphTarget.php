<?php

namespace App\Domain\ValueObjects;

final readonly class MorphTarget
{
    public function __construct(
        public string $type,
        public int $id,
    ) {}
}
