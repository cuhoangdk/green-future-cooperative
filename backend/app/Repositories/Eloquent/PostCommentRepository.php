<?php

namespace App\Repositories\Eloquent;

use App\Models\PostComment;
use App\Repositories\Contracts\PostCommentRepositoryInterface;

class PostCommentRepository implements PostCommentRepositoryInterface
{
    protected $model;

    public function __construct(PostComment $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10)
    {
        return $this->model->with('customer')->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getTrashed(string $sortBy = 'deleted_at', string $sortDirection = 'desc', int $perPage = 10)
    {
        return $this->model->onlyTrashed()->with('customer')->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }
    public function getByPostAndId($postId, $id)
    {
        $comment=$this->model->where('post_id', $postId)->where('id', $id)->first();
        \Log::info('Fetching comment', ['id' => $id, 'comment' => $comment]);
        return $comment;
    }


    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $comment = $this->model->find($id);
        if ($comment) {
            $comment->update($data);
            return $comment;
        }
        return null;
    }

    public function delete($id)
    {
        $comment = $this->model->find($id);
        if ($comment) {
            $comment->delete();
            return true;
        }
        return false;
    }

    public function getTrashedById($id)
    {
        return $this->model->onlyTrashed()->find($id);
    }

    public function restore($id)
    {
        $comment = $this->model->onlyTrashed()->find($id);
        if ($comment) {
            return $comment->restore();
        }
        return false;
    }

    public function forceDelete($id)
    {
        $comment = $this->model->onlyTrashed()->find($id);
        if ($comment) {
            return $comment->forceDelete();
        }
        return false;
    }

    public function getAllComments(int $postId)
    {
        return $this->model->where('post_id', $postId)->with('customer')->latest()->get();
    }
}
