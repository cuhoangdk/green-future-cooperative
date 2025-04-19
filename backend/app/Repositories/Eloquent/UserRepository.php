<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Hash;
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
            ->with('address')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }

    /**
     * Lấy user theo ID.
     */
    public function getById($id)
    {
        return $this->model->with('address')->find($id);
    }



    /**
     * Tạo user mới.
     */
    public function create(array $data)
    {
        // Tách dữ liệu address nếu có
        $addressData = null;
        if (isset($data['address'])) {
            $addressData = [
                'province' => $data['address']['province'] ?? null,
                'district' => $data['address']['district'] ?? null,
                'ward' => $data['address']['ward'] ?? null,
                'street_address' => $data['address']['street_address'] ?? null,
            ];
            unset($data['address']); // Loại bỏ address khỏi data chính
        }

        // Tạo user mới
        $user = $this->model->create($data);

        // Tạo address nếu có
        if ($addressData) {
            $user->address()->create($addressData);
        }

        return $user;
        
    }

    /**
     * Cập nhật thông tin user.
     */
    public function update($id, array $data)
    {
        $user = $this->model->find($id);
        if ($user) {
            // Tách dữ liệu address nếu có
            $addressData = [
                'province' => $data['address']['province'] ?? null,
                'district' => $data['address']['district'] ?? null,
                'ward' => $data['address']['ward'] ?? null,
                'street_address' => $data['address']['street_address'] ?? null,
            ];
            unset($data['address']); // Loại bỏ address khỏi data chính

            // Cập nhật thông tin user
            $user->update($data);

            // Cập nhật hoặc tạo address nếu có
            if ($addressData) {
                $user->address()->updateOrCreate(
                    ['addressable_id' => $user->id, 'addressable_type' => get_class($user)],
                    $addressData
                );
            }

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
            ->with('address')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }
    
    /**
     * Lấy user đã bị xóa (soft delete).
     */
    public function getTrashedById($id)
    {
        return $this->model->with('address')->onlyTrashed()->find($id);
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
        $query = $this->model->with('address');

        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")                    
                    ->orwhereHas('address', function (Builder $q) use ($search) {
                        $q->where('street_address', 'like', "%{$search}%");
                    });
            });
        });
       
        $query->when(isset($filters['is_super_admin']), fn($query) => $query->where('is_super_admin', $filters['is_super_admin']));
        $query->when(isset($filters['is_banned']), fn($query) => $query->where('is_banned', $filters['is_banned']));

        // Lọc theo địa phương (province, district, ward phải là số)
        $query->when(isset($filters['province']), function ($query) use ($filters) {
            $query->whereHas('address', function (Builder $q) use ($filters) {
                $q->where('province', $filters['province']);
            });
        });
    
        $query->when(isset($filters['district']), function ($query) use ($filters) {
            $query->whereHas('address', function (Builder $q) use ($filters) {
                $q->where('district', $filters['district']);
            });
        });
    
        $query->when(isset($filters['ward']), function ($query) use ($filters) {
            $query->whereHas('address', function (Builder $q) use ($filters) {
                $q->where('ward', $filters['ward']);
            });
        });
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
        $query = $this->model->with('address');

        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");                    
                    
            });
        });

        $query->orderBy(
            $sortBy,
            $sortDirection
        );

        return $query->paginate($perPage);
    }
    public function changePassword(int $id, array $data): bool
    {
        $customer = $this->model->find($id);       

        return $customer->update(['password' => Hash::make($data['password'])]);
    }
}
