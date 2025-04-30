<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\IndexOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Models\User;

class OrderController extends Controller
{
    protected $orderRepository;
    protected $notificationRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function index(IndexOrderRequest $request)
    {
        $customerId = auth('api_customers')->id();
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'updated_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $orders = $this->orderRepository->getAll($customerId, $sortBy, $sortDirection, $perPage)
            ->appends(request()->query());
        
        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        $customerId = auth('api_customers')->id();
        try {
            $order = $this->orderRepository->createForCustomer($customerId, $request->validated());

            // Gửi thông báo
            $this->sendOrderNotifications($order, 'tạo');

            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $customerId = auth('api_customers')->id();
        $order = $this->orderRepository->getById($customerId, $id);
        return new OrderResource($order);
    }

    public function cancel(CancelOrderRequest $request, $id)
    {
        $customerId = auth('api_customers')->id();
        try {
            $order = $this->orderRepository->cancel($customerId, $id, $request->validated());

            // Gửi thông báo
            $this->sendOrderNotifications($order, 'huỷ');

            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    protected function sendOrderNotifications($order, $status)
    {
        // Thông báo cho customer
        $this->notificationRepository->create([
            'user_type' => 'customer',
            'user_id' => $order->customer_id,
            'title' => "Đơn hàng #{$order->id} đã được {$status}",
            'type' => 'order_status',            
        ]);

        // Thông báo cho users liên quan đến sản phẩm (dựa trên items)
        $productUserIds = collect($order->items)->pluck('product.user_id')->unique();
        foreach ($productUserIds as $userId) {
            $this->notificationRepository->create([
                'user_type' => 'member',
                'user_id' => $userId,
                'title' => "Đơn hàng #{$order->id} chứa sản phẩm của bạn đã được {$status}",
                'type' => 'order_status',                
            ]);
        }

        // Thông báo cho super admins
        $superAdmins = User::where('is_super_admin', true)->get();
        foreach ($superAdmins as $superAdmin) {
            $this->notificationRepository->create([
                'user_type' => 'member',
                'user_id' => $superAdmin->id,
                'title' => "Đơn hàng #{$order->id} đã được {$status}",
                'type' => 'order_status',                
            ]);
        }
    }
}