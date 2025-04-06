<?php

namespace App\Repositories\Contracts;
interface ProductCatogoryRepositoryInterface extends BaseRepositoryInterface
{   
    public function getBySlug($slug);
}