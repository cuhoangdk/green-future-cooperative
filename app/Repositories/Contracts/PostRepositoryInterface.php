<?php

namespace App\Repositories\Contracts;

interface PostRepositoryInterface extends CRUDRepositoryInterface
{   
    public function getBySlug($slug);
    public function getByCategory($categoryId);
}
