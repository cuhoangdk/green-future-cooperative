<?php
namespace App\Repositories\Contracts;

use App\Models\Farm;
use Illuminate\Pagination\Paginator;

interface FarmRepositoryInterface extends BaseRepositoryInterface
{
    public function search(string $sortBy = 'created_at',
    string $sortDirection = 'desc',
    int $perPage = 10,
    array $filters = []);
}
