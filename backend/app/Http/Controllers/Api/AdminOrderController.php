<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    protected $repository;

    public function __construct(OrderRepositoryInterface $repository)
    {
        $this->repository = $repository;        
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $orders = $this->repository->getAll(null, $perPage);
        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $data = $request->validated();
            $customerId = $data['customer_id'];
            $order = $this->repository->createForAdmin($customerId, $data);
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $order = $this->repository->getById(null, $id);
        return new OrderResource($order);
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        $order = $this->repository->update(null, $id, $request->validated());
        return new OrderResource($order);
    }

    public function cancel(CancelOrderRequest $request, $id)
    {        
        try {
            $order = $this->repository->cancel(null, $id, $request->all());
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}