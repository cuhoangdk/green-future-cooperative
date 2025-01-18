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

}
