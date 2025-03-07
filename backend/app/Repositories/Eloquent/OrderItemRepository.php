<?php
namespace App\Repositories\Eloquent;

use App\Models\OrderItem;
use App\Repositories\Contracts\OrderItemRepositoryInterface;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    protected $model;

    public function __construct(OrderItem $model)
    {
        $this->model = $model;
    }

    public function create(int $orderId, array $data)
    {
        $data['order_id'] = $orderId;
        return $this->model->create($data);
    }
}