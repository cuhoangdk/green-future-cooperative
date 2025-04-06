<?php

namespace App\Repositories\Contracts;
interface ProductRepositoryInterface extends BaseRepositoryInterface
{       
    public function getBySlug($slug);
    public function getByProductCode($productCode);
    public function getFilteredProduct(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    );
    public function searchByName(
        string $name,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    );
}