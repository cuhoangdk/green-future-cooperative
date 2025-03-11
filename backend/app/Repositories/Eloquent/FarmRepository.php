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
        return $this->model->with('address', 'user')->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->with('address', 'user')->find($id);
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
            $addressData = $data['address'] ?? null;
            unset($data['address']); 
            $user = Auth::guard('api_users')->user();
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
        return $this->model->where('id', $id)->delete();
    }
    public function getTrashed(
        string $sortBy = 'deleted_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ) {
        return $this->model->with('address', 'user')->onlyTrashed()
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }
    
    public function getTrashedById($id)
    {
        return $this->model->onlyTrashed()->find($id);
    }

    public function restore($id): bool
    {
        $category = $this->model->onlyTrashed()->find($id);
        if ($category) {
            $category->restore();
            return true;
        }
        return false;
    }

    public function forceDelete($id): bool
    {
        $category = $this->model->onlyTrashed()->find($id);
        if ($category) {
            $category->forceDelete();
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

        // Lọc theo từ khóa tìm kiếm
        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });

        $query->with('address', 'user')->orderBy(
            $sortBy,
            $sortDirection
        );

        return $query->paginate($perPage);
    }

    
}
