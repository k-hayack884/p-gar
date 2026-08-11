<?php

namespace App\Application\UseCases\Auth;

use App\Domain\Repositories\Auth\AuthRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Auth\AuthenticationException;

final class LoginUseCase
{
    public function __construct(
        private readonly AuthRepositoryInterface $authRepository,
    ) {}

    public function execute(string $email, string $password): User
    {
        $user = $this->authRepository->findByCredentials($email, $password);

        if ($user === null) {
            throw new AuthenticationException;
        }

        return $user;
    }
}
