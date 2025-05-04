<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\IndexOrderRequest;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Order\QuickStoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\SearchOrderRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Mockery\QuickDefinitionsConfiguration;

class AdminOrderController extends Controller
{
    protected $orderRepository;
    protected $notificationRepository;

    protected $statusTranslations = [
        'processing' => 'đang xử lý',
        'delivering' => 'đang giao',
        'delivered' => 'đã giao',
    ];

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
    public function search(SearchOrderRequest $request)
    {
        $customerId = auth('api_customers')->id();
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'updated_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $filters = [
            'search' => $request->input('search'),
            'year' => $request->input('year'),
            'month' => $request->input('month'),
            'day' => $request->input('day'),
            'status' => $request->input('status'),
        ];

        $orders = $this->orderRepository->search($customerId, $perPage, $filters, $sortBy, $sortDirection)
            ->appends(request()->query());

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $data = $request->validated();
            unset($data['status']);
            $customerId = $data['customer_id'];
            $order = $this->orderRepository->createForAdmin($customerId, $data);

            // Gửi thông báo
            $this->sendOrderNotifications($order, 'tạo');

            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function quickStore(QuickStoreOrderRequest $request)
    {
        try {
            // Lấy dữ liệu đã xác thực
            $data = $request->validated();            

            // Chuẩn bị dữ liệu đơn hàng
            $orderData = [
                "full_name" => null,
                "phone_number" => null,
                "customer_id" => null,
                "province" => null,
                "district" => null,
                "ward" => null,
                "street_address" => null,
                "email" => null,
                'status' => 'delivered', 
                'items' => [
                    [
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity'],
                    ],
                ],
            ];

            // Tạo đơn hàng thông qua repository
            $order = $this->orderRepository->createForAdmin(null, $orderData);

            // Gửi thông báo với trạng thái đã dịch
            $translatedStatus = $this->statusTranslations['delivered'];
            $this->sendOrderNotifications($order, $translatedStatus);

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
            $status = $request->validated()['status'];
            // Dịch trạng thái sang tiếng Việt, mặc định giữ nguyên nếu không tìm thấy
            $translatedStatus = $this->statusTranslations[$status] ?? $status;
            $this->sendOrderNotifications($order, $translatedStatus);
        }

        return new OrderResource($order);
    }

    public function cancel(CancelOrderRequest $request, $id)
    {        
        try {
            $order = $this->orderRepository->cancel(null, $id, $request->all());

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
        $superAdmins = User::where('is_super_admin', true)
                    ->whereNotIn('id', $productUserIds) // Bỏ qua super admin có trong productUserIds
                    ->get();
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