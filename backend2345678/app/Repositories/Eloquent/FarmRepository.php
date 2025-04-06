<?php

namespace App\Repositories\Eloquent;

use App\Models\Farm;
use App\Repositories\Contracts\FarmRepositoryInterface;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

class FarmRepository implements FarmRepositoryInterface
{
    protected $model;

    public function __construct(Farm $farm)
    {
        $this->model = $farm;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10)
    {
        $query = $this->model->with('address', 'user');
        
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin) {
            $query->where('user_id', $user->id);
        }
        
        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getById($id)
    {
        $farm = $this->model->with('address', 'user')->find($id);

        
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin && $farm && $farm->user_id !== $user->id) {
            return null; 
        }        

        return $farm;
    }

    public function create(array $data)
    {
        $addressData = $data['address'] ?? null;        
        unset($data['address']);
        
        // Kiểm tra quyền super admin nếu có thay đổi user_id
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin) {
            $data['user_id'] = $user->id;
        }
        
        $farm = $this->model->create($data);
        if ($addressData) {
            $farm->address()->create($addressData);
        }

        return $farm;
    }

    public function update($id, array $data)
    {
        $farm = $this->model->find($id);        
        if ($farm) {
            // Kiểm tra quyền chỉnh sửa farm
            $user = Auth::guard('api_users')->user();
            if ($user && !$user->is_super_admin && $farm->user_id !== $user->id) {
                return null; // Hoặc throw exception nếu cần
            }

            $addressData = $data['address'] ?? null;
            unset($data['address']); 
            if ($user && !$user->is_super_admin) {
                $data['user_id'] = $user->id;
            }
            $farm->update($data);
            if ($addressData) {
                $farm->address()->updateOrCreate(
                    ['addressable_id' => $farm->id, 'addressable_type' => get_class($farm)],
                    $addressData
                );
            }

            return $farm;
        }

        return null;
    }

    public function delete($id)
    {
        $farm = $this->model->find($id);
        if ($farm) {
            // Kiểm tra quyền xóa farm
            $user = Auth::guard('api_users')->user();
            if ($user && !$user->is_super_admin && $farm->user_id !== $user->id) {
                return false;
            }
            return $farm->delete();
        }
        return false;
    }

    public function getTrashed(
        string $sortBy = 'deleted_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ) {
        $query = $this->model->with('address', 'user')->onlyTrashed();

        // Chỉ giới hạn cho user không phải super_admin
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }
    
    public function getTrashedById($id)
    {
        $farm = $this->model->onlyTrashed()->find($id);

        // Kiểm tra quyền truy cập farm đã xóa
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin && $farm && $farm->user_id !== $user->id) {
            return null;
        }

        return $farm;
    }

    public function restore($id): bool
    {
        $farm = $this->model->onlyTrashed()->find($id);
        if ($farm) {
            // Kiểm tra quyền khôi phục farm
            $user = Auth::guard('api_users')->user();
            if ($user && !$user->is_super_admin && $farm->user_id !== $user->id) {
                return false;
            }
            $farm->restore();
            return true;
        }
        return false;
    }

    public function forceDelete($id): bool
    {
        $farm = $this->model->onlyTrashed()->find($id);
        if ($farm) {
            // Kiểm tra quyền xóa vĩnh viễn farm
            $user = Auth::guard('api_users')->user();
            if ($user && !$user->is_super_admin && $farm->user_id !== $user->id) {
                return false;
            }
            $farm->forceDelete();
            return true;
        }
        return false;
    }

    public function search(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ) {
        $query = $this->model->query();

        // Chỉ giới hạn cho user không phải super_admin
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin) {
            $query->where('user_id', $user->id);
        }

        // Lọc theo từ khóa tìm kiếm
        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('full_name', 'like', "%{$search}%");
                });
            });
        });
        // Lọc theo user_id nếu là super_admin và có tham số user_id
        $query->when($user && $user->is_super_admin && isset($filters['user_id']), function (Builder $query) use ($filters) {
            $query->where('user_id', $filters['user_id']);
        });

        $query->with('address', 'user')->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }
}