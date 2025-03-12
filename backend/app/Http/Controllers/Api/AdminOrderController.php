<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
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

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $orders = $this->orderRepository->getAll(null, $perPage);
        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $data = $request->validated();
            $customerId = $data['customer_id'];
            $order = $this->orderRepository->createForAdmin($customerId, $data);

            // Gửi thông báo
            $this->sendOrderNotifications($order, 'created');

            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $order = $this->orderRepository->getById(null, $id);
        return new OrderResource($order);
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        $order = $this->orderRepository->update(null, $id, $request->validated());

        // Gửi thông báo nếu trạng thái thay đổi
        if ($request->has('status')) {
            $this->sendOrderNotifications($order, $request->validated()['status']);
        }

        return new OrderResource($order);
    }

    public function cancel(CancelOrderRequest $request, $id)
    {        
        try {
            $order = $this->orderRepository->cancel(null, $id, $request->all());

            // Gửi thông báo
            $this->sendOrderNotifications($order, 'cancelled');

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