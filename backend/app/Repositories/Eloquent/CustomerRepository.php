<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Hash;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    protected $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10)
    {
        return $this->model->with(['addresses.address'])->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->with(['addresses.address'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $customer = $this->model->find($id);
        if ($customer) {
            $customer->update($data);
            return $customer;
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
        return $this->model->onlyTrashed()->with(['addresses.address'])
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }
    
    public function getTrashedById($id)
    {
        return $this->model->with(['addresses.address'])->onlyTrashed()->find($id);
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
    public function changePassword(int $id, array $data): bool
    {
        $customer = $this->model->find($id);       

        return $customer->update(['password' => $data['password']]);
    }
    public function search(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ) {
        $query = $this->model->query();
    
        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('addresses', function (Builder $q) use ($search) {
                        $q->whereHas('address', function (Builder $q) use ($search) {
                              $q->where('street_address', 'like', "%{$search}%");
                          });
                    });
            });
        });
    
        $query->when(isset($filters['province']), function ($query) use ($filters) {
            $query->whereHas('addresses.address', function (Builder $q) use ($filters) {
                $q->where('province', $filters['province']);
            });
        });
    
        $query->when(isset($filters['district']), function ($query) use ($filters) {
            $query->whereHas('addresses.address', function (Builder $q) use ($filters) {
                $q->where('district', $filters['district']);
            });
        });
    
        $query->when(isset($filters['ward']), function ($query) use ($filters) {
            $query->whereHas('addresses.address', function (Builder $q) use ($filters) {
                $q->where('ward', $filters['ward']);
            });
        });
    
        $query->with(['addresses.address'])->orderBy(
            $sortBy,
            $sortDirection
        );
    
        return $query->paginate($perPage);
    }
    
}
