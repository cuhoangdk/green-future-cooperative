<?php
namespace App\Repositories\Eloquent;

use App\Models\Farm;
use App\Repositories\Contracts\FarmRepositoryInterface;
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
        return $this->model->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->find($id);
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
        return $this->model->onlyTrashed()
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
                $q->where('name', 'like', "%{$search}%")                    
                    ->orWhere('street_address', 'like', "%{$search}%");
            });
        });

        $query->orderBy(
            $this->validateSortColumn($sortBy),
            $this->validateSortDirection($sortDirection)
        );

        return $query->paginate($perPage);
    }

    private function validateSortColumn(string $column): string
    {
        $allowedColumns = ['name', 'province', 'district', 'ward', 'created_at','updated_at'];
        return in_array($column, $allowedColumns) ? $column : 'created_at';
    }

    private function validateSortDirection(string $direction): string
    {
        return in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';
    }
}
