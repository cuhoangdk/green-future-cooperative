<?php

namespace App\Repositories\Contracts;

interface ProductQuantityPriceRepositoryInterface
{
    public function getAll(string $productId, string $sortBy = 'quantity', string $sortDirection = 'asc', int $perPage = 10);
    public function getTrashed(string $productId, string $sortBy = 'deleted_at', string $sortDirection = 'desc', int $perPage = 10);
    public function getById(string $productId, $id);
    public function create(string $productId, array $data);
    public function update(string $productId, $id, array $data);
    public function delete(string $productId, $id);
    public function restore(string $productId, $id);
    public function forceDelete(string $productId, $id);
}