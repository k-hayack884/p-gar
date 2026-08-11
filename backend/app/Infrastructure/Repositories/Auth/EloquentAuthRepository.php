<?php

namespace App\Infrastructure\Repositories\Auth;

use App\Domain\Repositories\Auth\AuthRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Auth;

final class EloquentAuthRepository implements AuthRepositoryInterface
{
    public function findByCredentials(string $email, string $password): ?User
    {
        if (! Auth::guard('web')->attempt([
            'email' => $email,
            'password' => $password,
        ])) {
            return null;
        }

        $user = Auth::guard('web')->user();

        return $user instanceof User ? $user : null;
    }
}
