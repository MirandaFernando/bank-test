<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function getUserById(int $id): User;
    public function updateUser(User $user, array $data): User;
    public function deleteUser(User $user): bool;
    public function logoutUser(): void;
    public function invalidateSession(Request $request): void;
}
