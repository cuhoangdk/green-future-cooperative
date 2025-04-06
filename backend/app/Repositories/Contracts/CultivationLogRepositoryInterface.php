<?php

namespace App\Repositories\Contracts;

interface CultivationLogRepositoryInterface
{
    public function getAll(
        int $productId,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    );
    public function getById(int $productId, $id); 
    public function create(int $productId, array $data); 
    public function update(int $productId, $id, array $data); 
    public function delete(int $productId, $id); 
}