<?php

namespace App\Repositories\Eloquent;

use App\Models\OrderHistory;
use App\Repositories\Contracts\OrderHistoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderHistoryRepository implements OrderHistoryRepositoryInterface
{
    protected $model;

    public function __construct(OrderHistory $model)
    {
        $this->model = $model;
    }

    public function create(array $data): OrderHistory
    {
        return $this->model->create($data);
    }

    public function getByOrderId(string $orderId): Collection
    {
        return $this->model->where('order_id', $orderId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}