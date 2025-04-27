<?php

namespace App\Repositories\Contracts;

interface ContactMessageRepositoryInterface
{
    public function getAll(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    );

    public function getById($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}