<?php

namespace App\Repositories\Contracts;
use Illuminate\Contracts\Pagination\Paginator;
interface PostCategoryRepositoryInterface extends BaseRepositoryInterface
{   
    public function getBySlug($slug);
}