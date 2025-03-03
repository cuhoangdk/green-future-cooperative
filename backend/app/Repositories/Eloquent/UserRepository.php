<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Lấy danh sách tất cả users với phân trang.
     */
    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10): Paginator
    {
        return $this->model
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }

    /**
     * Lấy user theo ID.
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Lấy user theo usercode.
     */
    public function getByUsercode($usercode)
    {
        return $this->model->where('usercode', $usercode)->first();
    }

    /**
     * Tạo user mới.
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Cập nhật thông tin user.
     */
    public function update($id, array $data)
    {
        $user = $this->model->find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    /**
     * Xóa user (soft delete).
     */
    public function delete($id)
    {
        $user = $this->model->find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }
    public function getTrashed(
        string $sortBy = 'deleted_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ) {
        return $this->model->onlyTrashed()
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }
    
    /**
     * Lấy user đã bị xóa (soft delete).
     */
    public function getTrashedById($id)
    {
        return $this->model->onlyTrashed()->find($id);
    }

    /**
     * Khôi phục user đã bị xóa.
     */
    public function restore($id)
    {
        $user = $this->model->onlyTrashed()->find($id);
        if ($user) {
            return $user->restore();
        }
        return false;
    }

    /**
     * Xóa vĩnh viễn user.
     */
    public function forceDelete($id)
    {
        $user = $this->model->onlyTrashed()->find($id);
        if ($user) {
            return $user->forceDelete();
        }
        return false;
    }

    /**
     * Lọc và phân trang danh sách users.
     */
    public function getFilteredUsers(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ): Paginator {
        $query = $this->model->query();

        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('usercode', 'like', "%{$search}%")
                    ->orWhere('street_address', 'like', "%{$search}%");
            });
        });
       
        $query->when(isset($filters['is_super_admin']), fn($query) => $query->where('is_super_admin', $filters['is_super_admin']));
        $query->when(isset($filters['is_banned']), fn($query) => $query->where('is_banned', $filters['is_banned']));

        // Lọc theo địa phương (province, district, ward phải là số)
        $query->when(isset($filters['province']), fn($query) => $query->where('province', $filters['province']));
        $query->when(isset($filters['district']), fn($query) => $query->where('district', $filters['district']));
        $query->when(isset($filters['ward']), fn($query) => $query->where('ward', $filters['ward']));

        $query->orderBy(
            $sortBy,
            $sortDirection
        );

        return $query->paginate($perPage);
    }

    /**
     * Tìm kiếm users đơn giản.
     */
    public function getSearchUsers(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ): Paginator {
        $query = $this->model->query();

        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('usercode', 'like', "%{$search}%")
                    ->orWhere('street_address', 'like', "%{$search}%");
            });
        });

        $query->orderBy(
            $sortBy,
            $sortDirection
        );

        return $query->paginate($perPage);
    }
}
