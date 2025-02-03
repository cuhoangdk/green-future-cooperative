<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
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
        ?string $search = null,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ) {
        $query = Post::query()
            ->with(['category', 'author'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('summary', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhereHas('author', function ($authorQuery) use ($search) {
                          $authorQuery->where('name', 'like', "%{$search}%"); // Giả sử trường tên tác giả là 'name'
                      });
                });
            })
            ->orderBy($this->validateSortColumn($sortBy), $this->validateSortDirection($sortDirection));
    
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
