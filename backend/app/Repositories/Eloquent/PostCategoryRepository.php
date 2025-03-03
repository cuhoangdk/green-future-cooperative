<?php

namespace App\Repositories\Eloquent;

use App\Models\PostCategory;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;

class PostCategoryRepository implements PostCategoryRepositoryInterface
{
    protected $model;

    public function __construct(PostCategory $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = null)
    {
        $query = $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->orderBy($sortBy, $sortDirection);

        // Nếu $perPage không được cung cấp, trả về danh sách không phân trang
        return $perPage ? $query->paginate($perPage) : $query->get();
    }


    public function getById($id)
{
    return $this->model->when(!auth('api_users')->check(), function ($query) {
        $query->where('is_active', true);
    })->find($id); 
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
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->where('slug', $slug)->first();
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
    public function getFilteredCategories(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ) {
        $query = PostCategory::query();
        $query->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        });
        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
    
        // Sắp xếp
        $query->orderBy(
            $sortBy, 
            $sortDirection
        );
    
        return $query->paginate($perPage);
    }   
}
