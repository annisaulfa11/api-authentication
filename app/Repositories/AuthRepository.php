<?php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(array $data): User
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }

    public function login(string $username, string $password): ?User
    {
        try {
            DB::beginTransaction();
            $user = User::where('username', $username)->firstOrFail();
            if (Hash::check($password, $user->password)) {
                DB::commit();
                return $user;
            }

            return null;

        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Login failed: ' . $e->getMessage());
        }
    }


    public function logout(User $user): void
    {
        try {
            $user->tokens()->delete();
        } catch (Exception $e) {
            throw new Exception('Logout failed: ' . $e->getMessage());
        }
    }
}
