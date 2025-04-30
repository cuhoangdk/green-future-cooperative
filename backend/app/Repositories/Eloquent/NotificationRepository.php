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
            $query->where(function ($q) use ($userId) {
                $q->where('user_type', 'App\Models\User')
                    ->where('user_id', $userId);   
            });             
        } elseif ($userType === 'App\Models\Customer') {
            // Khách hàng chỉ nhận thông báo của chính họ
            $query->where('user_type', 'App\Models\Customer')
                  ->where('user_id', $userId);
        } else {
            $query->whereRaw('1 = 0');
        }
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