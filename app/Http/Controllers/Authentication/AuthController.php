<?php
namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class AuthController extends Controller
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authRepository->register($request->validated());
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Registration failed',

            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->authRepository->login($request->username, $request->password);

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Login failed',

            ], 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authRepository->logout(auth()->user());

            return response()->json([
                'message' => 'Logged out successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
            ], 500);
        }
    }
}
