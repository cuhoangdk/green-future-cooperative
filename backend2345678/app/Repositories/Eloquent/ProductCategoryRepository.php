<?php

namespace App\Repositories\Eloquent;

use App\Models\PostCategory;
use App\Models\ProductCategory;
use App\Repositories\Contracts\ProductCatogoryRepositoryInterface;

class ProductCategoryRepository implements ProductCatogoryRepositoryInterface
{
    protected $model;

    public function __construct(ProductCategory $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = null)
    {
        $query = $this->model->orderBy($sortBy, $sortDirection);

        // Nếu $perPage không được cung cấp, trả về danh sách không phân trang
        return $perPage ? $query->paginate($perPage) : $query->get();
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
        $productCategory = $this->model->find($id);
        if ($productCategory) {
            $productCategory->update($data);
            return $productCategory;
        }
        return null;
    }

    public function delete($id): bool
    {
        $productCategory = $this->model->find($id);
        if ($productCategory) {
            $productCategory->delete();
            return true;
        }
        return false;
    }
    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
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
        
    
    
}
