<?php

namespace App\Repositories\Eloquent;

use App\Models\PostCategory;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;

class PostCategoryRepository implements PostCategoryRepositoryInterface
{
    protected $model;
    private int $perPage=10; 

    public function __construct(PostCategory $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10): Paginator
    {
        return $this->model
            ->orderBy($this->validateSortColumn($sortBy), $this->validateSortDirection($sortDirection))
            ->paginate($perPage);
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
        $category = $this->model->find($id);
        if ($category) {
            $category->update($data);
            return $category;
        }
        return null;
    }

    public function delete($id): bool
    {
        $category = $this->model->find($id);
        if ($category) {
            $category->delete();
            return true;
        }
        return false;
    }
    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
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
    public function getFilteredCategories(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ) {
        $query = PostCategory::query();
    
        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
    
        // Sắp xếp
        $query->orderBy(
            $this->validateSortColumn($sortBy), 
            $this->validateSortDirection($sortDirection)
        );
    
        return $query->paginate($perPage);
    }
    
    private function validateSortColumn(string $column): string
    {
        $allowedColumns = ['name', 'created_at', 'updated_at'];
        return in_array($column, $allowedColumns) ? $column : 'created_at';
    }
    
    private function validateSortDirection(string $direction): string
    {
        return in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';
    }
    
}
