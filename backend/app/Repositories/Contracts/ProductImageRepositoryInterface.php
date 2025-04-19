<?php

namespace App\Repositories\Contracts;

interface ProductImageRepositoryInterface
{
    public function getAll(string $productId, string $sortBy = 'sort_order', string $sortDirection = 'asc', int $perPage = 10);
    public function getById(string $productId, $id);
    public function create(string $productId, array $data);
    public function update(string $productId, $id, array $data);
    public function delete(string $productId, $id);
}