<?php

namespace App\Repositories\Contracts;
interface ProductRepositoryInterface 
{       
    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10);
    public function getTrashed(
        string $sortBy = 'deleted_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    );
    public function getById(string $id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getTrashedById($id);    
    public function restore($id);
    public function forceDelete($id);
    public function getBySlug($slug);
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