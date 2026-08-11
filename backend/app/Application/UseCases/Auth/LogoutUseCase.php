<?php

namespace App\Application\UseCases\Auth;

use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final class LogoutUseCase
{
    public function execute(User $user): void
    {
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();
    }
}
