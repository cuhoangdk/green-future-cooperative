<?php

namespace App\Repositories\Contracts;

use App\Models\OrderHistory;

interface OrderHistoryRepositoryInterface
{
    public function create(array $data): OrderHistory;
    public function getByOrderId(string $orderId);
}