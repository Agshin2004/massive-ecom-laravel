<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface IUserOwnedRepository
{
    public function getAllForUser(User $user): iterable;

    public function findForUser(int|string $id, User $user): ?Model;

    public function createForUser(array $data, ?User $user): object|array;

    public function updateForUser(int|string $id, array $data, User $user): bool;

    public function deleteForUser(int|string $id, User $user): bool;
}
