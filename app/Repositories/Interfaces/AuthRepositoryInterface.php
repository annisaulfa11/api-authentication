<?php
namespace App\Repositories\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function register(array $data): User;

    public function login(string $username, string $password): ?User;

    public function logout(User $user): void;
}

