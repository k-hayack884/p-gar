<?php

namespace App\Domain\ValueObjects;

enum ImageableType: string
{
    case Site = 'site';
    case Zone = 'zone';
    case Plant = 'plant';
    case WorkLog = 'work_log';
}
