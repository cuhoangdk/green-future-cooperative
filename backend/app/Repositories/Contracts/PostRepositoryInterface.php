<?php

namespace App\Repositories\Contracts;
use Illuminate\Contracts\Pagination\Paginator;
interface PostRepositoryInterface extends BaseRepositoryInterface
{    
    public function getBySlug($slug);
    public function getByCategory(
        $categoryId,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
    ): Paginator;
    public function getFilteredPosts(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    );
    public function getHotPosts(int $limit = 5);
    public function getFeaturedPosts(int $limit = 5);
    public function getCategoryBySlug(string $slug);

}
