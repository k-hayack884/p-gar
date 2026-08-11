<?php

namespace App\Domain\Repositories\Auth;

use App\Infrastructure\Models\User;

interface AuthRepositoryInterface
{
    public function findByCredentials(string $email, string $password): ?User;
}
