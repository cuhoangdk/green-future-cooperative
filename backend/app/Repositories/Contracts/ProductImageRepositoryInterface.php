<?php

namespace App\Repositories\Contracts;

interface ProductImageRepositoryInterface
{
    public function getAll(int $productId, string $sortBy = 'sort_order', string $sortDirection = 'asc', int $perPage = 10);
    public function getById(int $productId, $id);
    public function create(int $productId, array $data);
    public function update(int $productId, $id, array $data);
    public function delete(int $productId, $id);
}