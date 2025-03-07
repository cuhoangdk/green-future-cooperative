<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderController extends Controller
{
    protected $repository;

    public function __construct(OrderRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $customerId = auth('api_customers')->id();
        $orders = $this->repository->getAll($customerId);
        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        $customerId = auth('api_customers')->id();
        try {
            $order = $this->repository->createForCustomer($customerId, $request->validated());
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $customerId = auth('api_customers')->id();
        $order = $this->repository->getById($customerId, $id);
        return new OrderResource($order);
    }

    public function cancel(CancelOrderRequest $request, $id)
    {
        $customerId = auth('api_customers')->id();
        try {
            $order = $this->repository->cancel($customerId, $id, $request->validated());
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}