<?php

namespace App\Http\Controllers\Auth;

use App\Application\UseCases\Auth\LoginUseCase;
use App\Application\UseCases\Auth\LogoutUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Infrastructure\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginUseCase $loginUseCase,
        private readonly LogoutUseCase $logoutUseCase,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $user = $this->loginUseCase->execute($credentials['email'], $credentials['password']);

        $request->session()->regenerate();

        return response()->json($user);
    }

    public function logout(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $this->logoutUseCase->execute($user);

        return response()->noContent();
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
