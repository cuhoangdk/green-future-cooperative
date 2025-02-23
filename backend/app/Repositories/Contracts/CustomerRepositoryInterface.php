<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface CustomerRepositoryInterface extends BaseRepositoryInterface{
    public function changePassword(int $id, array $data);

}