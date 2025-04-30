<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderHistory\StoreOrderHistoryRequest;
use App\Http\Resources\OrderHistoryResource;
use App\Repositories\Contracts\OrderHistoryRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    protected $orderHistoryRepository;
    protected $orderRepository;

    public function __construct(
        OrderHistoryRepositoryInterface $orderHistoryRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request, string $orderId)
    {
        // Kiểm tra quyền truy cập cho khách hàng
        if (auth('api_customers')->check()) {
            $customerId = auth('api_customers')->id();
            // Xác minh đơn hàng thuộc về khách hàng
            $order = $this->orderRepository->getById($customerId, $orderId);
            if (!$order) {
                return response()->json(['message' => 'Order not found or not authorized'], 403);
            }
        }

        $histories = $this->orderHistoryRepository->getByOrderId($orderId);
        return OrderHistoryResource::collection($histories);
    }
}