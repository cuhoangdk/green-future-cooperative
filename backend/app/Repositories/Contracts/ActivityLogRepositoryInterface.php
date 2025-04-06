<?php

namespace App\Repositories\Contracts;

interface ActivityLogRepositoryInterface
{
    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10);

    public function getById($id);

    public function create(array $data);
    public function getFilteredActivityLog(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    );
}