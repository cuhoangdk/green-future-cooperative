<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductUnit;
use App\Repositories\Contracts\ProductUnitRepositoryInterface;

class ProductUnitRepository implements ProductUnitRepositoryInterface
{
    protected $model;

    public function __construct(ProductUnit $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = null)
    {
        return $this->model->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $productUnit = $this->model->find($id);
        if ($productUnit) {
            $productUnit->update($data);
            return $productUnit;
        }
        return null;
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }
    public function getTrashed(
        string $sortBy = 'deleted_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ) {
        return $this->model->onlyTrashed()
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }
    
    public function getTrashedById($id)
    {
        return $this->model->onlyTrashed()->find($id);
    }

    public function restore($id): bool
    {
        $category = $this->model->onlyTrashed()->find($id);
        if ($category) {
            $category->restore();
            return true;
        }
        return false;
    }

    public function forceDelete($id): bool
    {
        $category = $this->model->onlyTrashed()->find($id);
        if ($category) {
            $category->forceDelete();
            return true;
        }
        return false;
    }    

    private function validateSortColumn(string $column): string
    {
        $allowedColumns = ['name', 'created_at','updated_at'];
        return in_array($column, $allowedColumns) ? $column : 'created_at';
    }

    private function validateSortDirection(string $direction): string
    {
        return in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';
    }

}
