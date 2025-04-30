<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository implements NotificationRepositoryInterface
{
    protected $model;

    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    public function getAllForUser(string $userType, int $userId, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->query();

        \Log::debug('getAllForUser input', ['userType' => $userType, 'userId' => $userId]);

        if ($userType === 'App\Models\User') {
            $user = Auth::guard('api_users')->user();
            if ($user && $user->is_super_admin) {
                // Super admin nhận thông báo của họ và các loại thông báo cụ thể
                $query->where(function ($q) use ($userId) {
                    $q->where('user_type', 'App\Models\User')
                      ->where('user_id', $userId);
                });
            } else {
                // Người dùng không phải super admin nhận thông báo của họ và order_status liên quan đến sản phẩm
                $query->where(function ($q) use ($userId) {
                    $q->where('user_type', 'App\Models\User')
                      ->where('user_id', $userId)
                      ->where(function ($subQuery) use ($userId) {
                          $subQuery->whereNotIn('type', ['order_status'])
                                   ->orWhere(function ($orderStatusQuery) use ($userId) {
                                       $orderStatusQuery->where('type', 'order_status')
                                                        ->where('title', 'LIKE', 'Đơn hàng #%')
                                                        ->whereExists(function ($existsQuery) use ($userId) {
                                                            $existsQuery->selectRaw(1)
                                                                        ->from('orders')
                                                                        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                                                                        ->join('products', 'order_items.product_id', '=', 'products.id')
                                                                        ->whereRaw('orders.id = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(notifications.title, "#", -1), " ", 1) AS UNSIGNED)')
                                                                        ->where('products.user_id', $userId);
                                                        });
                                   });
                      });
                });
            }
        } elseif ($userType === 'App\Models\Customer') {
            // Khách hàng chỉ nhận thông báo của chính họ
            $query->where('user_type', 'App\Models\Customer')
                  ->where('user_id', $userId);
        } else {
            // Loại người dùng không hợp lệ, trả về tập rỗng
            $query->whereRaw('1 = 0');
        }

        \Log::debug('Notification query executed', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        // Chuẩn hóa user_type thành tên lớp đầy đủ
        if (isset($data['user_type'])) {
            if ($data['user_type'] === 'member') {
                $data['user_type'] = 'App\Models\User';
            } elseif ($data['user_type'] === 'customer') {
                $data['user_type'] = 'App\Models\Customer';
            }
        }
        return $this->model->create($data);
    }

    public function markAsRead(int $id)
    {
        $notification = $this->getById($id);
        if ($notification && !$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
        return $notification;
    }

    public function markAllAsRead(string $userType, int $userId)
    {
        $query = $this->model->where('user_type', $userType)->where('user_id', $userId);
        if ($userType === 'App\Models\User') {
            $user = Auth::guard('api_users')->user();
            if ($user && !$user->is_super_admin) {
                // Người dùng không phải super admin không thể đánh dấu new_customer/new_product là đã đọc
                $query->whereNotIn('type', ['new_customer', 'new_product']);
            }
        }
        return $query->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }


    public function delete(int $id)
    {
        $notification = $this->getById($id);
        if ($notification) {
            $notification->delete();
            return true;
        }
        return false;
    }
}