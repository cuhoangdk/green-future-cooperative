<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;

class PostRepository implements PostRepositoryInterface
{
    protected $model;
    private int $perPage=10; 

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function getAll(): Paginator
    {
        return $this->model->with(['category', 'author'])->paginate($this->perPage);
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
        $post = $this->model->find($id);
        if ($post) {
            $post->update($data);
            return $post;
        }
        return null;
    }

    public function delete($id)
    {
        $post = $this->model->find($id);
        if ($post) {
            $post->delete();
            return true;
        }
        return false;
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getByCategory($categoryId):Paginator
    {
        return $this->model->where('category_id', $categoryId)->with(['category', 'author'])->paginate($this->perPage);
    }    
    public function getTrashedById($id)
    {
        return $this->model->onlyTrashed()->find($id);
    }

    public function restore($id)
    {
        $post = $this->model->onlyTrashed()->find($id);
        if ($post) {
            return $post->restore();
        }
        return false;
    }

    public function forceDelete($id)
    {
        $post = $this->model->onlyTrashed()->find($id);
        if ($post) {
            return $post->forceDelete();
        }
        return false;
    }
    public function getFilteredPosts(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ) {
        $query = Post::query()
            ->with(['category', 'author'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('summary', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhereHas('author', function ($authorQuery) use ($search) {
                          $authorQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($filters['author'] ?? null, function ($query, $author) {
                $query->where('author_id', $author);
            })
            ->when($filters['category'] ?? null, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('post_status', $status);
            })
            ->when(($filters['start_date'] ?? null) && ($filters['end_date'] ?? null), 
                function ($query) use ($filters) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($filters['start_date'])->startOfDay(),
                        Carbon::parse($filters['end_date'])->endOfDay()
                    ]);
                },
                function ($query) use ($filters) {
                    $query->when($filters['start_date'] ?? null, function ($query, $startDate) {
                        $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
                    })->when($filters['end_date'] ?? null, function ($query, $endDate) {
                        $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
                    });
                }
            )
            ->when(isset($filters['is_hot']) && $filters['is_hot'] !== null, function ($query) use ($filters) {
                $query->where('is_hot', $filters['is_hot']);
            })
            ->when(isset($filters['is_featured']) && $filters['is_featured'] !== null, function ($query) use ($filters) {
                $query->where('is_featured', $filters['is_featured']);
            })
            ->orderBy(
                $this->validateSortColumn($sortBy), 
                $this->validateSortDirection($sortDirection)
            );
    
        return $query->paginate($perPage);
    }

    private function validateSortColumn(string $column): string
    {
        $allowedColumns = [
            'title', 'created_at', 'updated_at', 
            'published_at', 'post_status'
        ];
        
        return in_array($column, $allowedColumns) ? $column : 'created_at';
    }

    private function validateSortDirection(string $direction): string
    {
        return in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';
    }
}
