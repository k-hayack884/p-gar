<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class UserId
{
    public function __construct(public int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('User ID must be a positive integer.');
        }
    }
}
