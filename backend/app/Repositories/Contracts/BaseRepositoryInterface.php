<?php

namespace App\Repositories\Contracts;
use Illuminate\Contracts\Pagination\Paginator;

interface BaseRepositoryInterface
{
    public function getAll(): Paginator;
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
