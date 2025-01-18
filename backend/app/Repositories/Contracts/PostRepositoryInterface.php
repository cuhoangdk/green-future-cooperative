<?php

namespace App\Repositories\Contracts;
use Illuminate\Contracts\Pagination\Paginator;
interface PostRepositoryInterface extends BaseRepositoryInterface
{   
    public function getBySlug($slug);
    public function getByCategory($categoryId): Paginator;
}
