<?php

namespace App\Repositories\Contracts;

interface PostCommentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByPostAndId($postId, $id);
    public function getAllComments(int $postId);
    public function searchComments(string $keyword, int $postId, int $perPage = 10);
}
