<?php

namespace App\Repositories;

interface IRepository
{
    public function getAll(?int $limit): iterable;

    public function getById(int|string $id): ?object;

    public function create(array $data): object;

    public function update(int|string $id, array $data): bool;

    public function delete(int|string $id): bool;
}
