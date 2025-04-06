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

        if ($userType === 'App\Models\User') {
            $user = Auth::guard('api_users')->user();
            if ($user && $user->is_super_admin) {
                // Super admin nhận tất cả thông báo
                $query->where(function ($q) use ($userId) { // Thêm 'use ($userId)'
                    $q->where('user_type', 'App\Models\User')
                      ->where('user_id', $userId)
                      ->orWhereIn('type', ['order_status', 'new_customer', 'new_product']);
                });
            } else {
                // User thường chỉ nhận thông báo của mình + order_status liên quan đến sản phẩm của họ
                $query->where(function ($q) use ($userId) { // Thêm 'use ($userId)'
                    $q->where('user_type', 'App\Models\User')
                      ->where('user_id', $userId)
                      ->orWhere(function ($q) use ($userId) {
                          $q->where('type', 'order_status')
                            ->whereExists(function ($query) use ($userId) {
                                $query->selectRaw(1)
                                      ->from('orders')
                                      ->join('order_product', 'orders.id', '=', 'order_product.order_id')
                                      ->join('products', 'order_product.product_id', '=', 'products.id')
                                      ->whereColumn('notifications.data->order_id', 'orders.id')
                                      ->where('products.user_id', $userId);
                            });
                      });
                });
            }
        } elseif ($userType === 'App\Models\Customer') {
            // Customer chỉ nhận thông báo của chính họ
            $query->where('user_type', 'App\Models\Customer')
                  ->where('user_id', $userId);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        // Chuyển user_type thành tên lớp đầy đủ
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
            if (!$user->is_super_admin) {
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