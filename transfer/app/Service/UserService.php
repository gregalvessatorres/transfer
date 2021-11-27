<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Http\Response;

class UserService
{
    public function createUser(array $userData): ?User
    {
        $user = User::create($userData);
        if (is_null($user)) {
            throw new \Exception('Error creating user', Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    public function destroy(int $id): void
    {
        $user = User::find($id);

        if (is_null($user)) {
            throw new \Exception('User not found', Response::HTTP_NOT_FOUND);
        }

        if ($user->wallet()->first()) {
            throw new \Exception('Users wallet must be removed first', Response::HTTP_NOT_FOUND);
        }

        $user->delete();
    }
}