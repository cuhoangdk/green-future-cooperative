<?php

namespace App\Repositories\Contracts;
use Illuminate\Contracts\Pagination\Paginator;
interface PostRepositoryInterface extends BaseRepositoryInterface
{   
    public function getBySlug($slug);
    public function getByCategory($categoryId): Paginator;
    public function getFilteredPosts(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    );
}
