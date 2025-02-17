<?php

namespace App\Repositories\Contracts;
use Illuminate\Contracts\Pagination\Paginator;
interface UserRepositoryInterface extends BaseRepositoryInterface
{    
    public function getByUsercode($usercode);
    public function getSearchUsers(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $search = []);
    public function getFilteredUsers(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    );
}