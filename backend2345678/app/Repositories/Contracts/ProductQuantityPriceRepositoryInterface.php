<?php

namespace App\Repositories\Contracts;

interface ProductQuantityPriceRepositoryInterface
{
    public function getAll(int $productId, string $sortBy = 'quantity', string $sortDirection = 'asc', int $perPage = 10);
    public function getTrashed(int $productId, string $sortBy = 'deleted_at', string $sortDirection = 'desc', int $perPage = 10);
    public function getById(int $productId, $id);
    public function create(int $productId, array $data);
    public function update(int $productId, $id, array $data);
    public function delete(int $productId, $id);
    public function restore(int $productId, $id);
    public function forceDelete(int $productId, $id);
}