<?php

namespace App\Repositories\Contracts;

interface PostRepositoryInterface extends BaseRepositoryInterface
{   
    public function getBySlug($slug);
    public function getByCategory($categoryId);
}
