<?php

namespace App\Repositories\Contracts;

interface CultivationLogRepositoryInterface
{
    public function getAll(
        string $productId,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    );
    public function getById(string $productId, $id); 
    public function create(string $productId, array $data); 
    public function update(string $productId, $id, array $data); 
    public function delete(string $productId, $id); 
}