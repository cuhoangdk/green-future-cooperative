<?php

namespace App\Repositories\Contracts;
interface PostCategoryRepositoryInterface extends BaseRepositoryInterface
{   
    public function getBySlug($slug);
    public function getFilteredCategories(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    );
}