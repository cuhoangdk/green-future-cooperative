<?php

namespace App\Repositories\Contracts;

interface PostCommentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByPostAndId($postId, $id);
    public function getAllComments(int $postId);
}
