<?php

namespace App\Domain\ValueObjects;

enum TargetType: string
{
    case Site = 'site';
    case Zone = 'zone';
    case Plant = 'plant';
}
