<?php

namespace App\Repositories\Contracts;

interface OrderItemRepositoryInterface
{
    public function create(int $orderId, array $data);
}