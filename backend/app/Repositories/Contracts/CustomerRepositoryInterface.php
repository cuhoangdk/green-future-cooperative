<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface CustomerRepositoryInterface extends BaseRepositoryInterface{
    public function changePassword(int $id, array $data);
    public function search(string $sortBy = 'created_at',
    string $sortDirection = 'desc',
    int $perPage = 10,
    array $filters = []);
}