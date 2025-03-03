<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->with(['category', 'unit', 'user'])
            ->orderBy($sortBy,
            $sortDirection)
            ->paginate($perPage);
    }

    public function getTrashed(
        string $sortBy = 'deleted_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->model->onlyTrashed()
            ->with(['category', 'unit', 'user'])
            ->orderBy($sortBy,
            $sortDirection)
            ->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->with(['category', 'unit', 'user'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $product = $this->getById($id);
            $product->update($data);
            return $product;
        });
    }

    public function delete($id)
    {
        $product = $this->getById($id);
        $product->delete();
        return $product;
    }

    public function getTrashedById($id)
    {
        return $this->model->onlyTrashed()
            ->with(['category', 'unit', 'user'])
            ->findOrFail($id);
    }

    public function restore($id)
    {
        $product = $this->getTrashedById($id);
        $product->restore();
        return $product;
    }

    public function forceDelete($id)
    {
        $product = $this->getTrashedById($id);
        $product->forceDelete();
        return true;
    }

    public function getBySlug($slug)
    {
        return $this->model->with(['category', 'unit', 'user'])->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function getByProductCode($productCode)
    {
        return $this->model->with(['category', 'unit', 'user'])->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })
            ->where('product_code', $productCode)
            ->firstOrFail();
    }

    public function getFilteredProduct(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ): LengthAwarePaginator {
        $query = $this->model->with(['category', 'unit', 'user']);

        // Áp dụng các bộ lọc nếu có
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($value !== null) {
                    switch ($key) {
                        case 'name':
                        case 'product_code':
                        case 'description':
                            $query->where($key, 'like', "%{$value}%");
                            break;
                        case 'category_id':
                        case 'unit_id':
                        case 'user_id':
                        case 'farm_id':
                            $query->where($key, $value);
                            break;
                        case 'pricing_type':
                            $query->whereIn($key, (array)$value);
                            break;
                        case 'is_active':
                            $query->where($key, filter_var($value, FILTER_VALIDATE_BOOLEAN));
                            break;
                        case 'base_price_min':
                            $query->where('base_price', '>=', $value);
                            break;
                        case 'base_price_max':
                            $query->where('base_price', '<=', $value);
                            break;
                        case 'stock_quantity_min':
                            $query->where('stock_quantity', '>=', $value);
                            break;
                        case 'stock_quantity_max':
                            $query->where('stock_quantity', '<=', $value);
                            break;
                        case 'sown_at_from':
                            $query->where('sown_at', '>=', $value);
                            break;
                        case 'sown_at_to':
                            $query->where('sown_at', '<=', $value);
                            break;
                        case 'harvested_at_from':
                            $query->where('harvested_at', '>=', $value);
                            break;
                        case 'harvested_at_to':
                            $query->where('harvested_at', '<=', $value);
                            break;
                    }
                }
            }
        }

        return $query->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->orderBy($sortBy,
        $sortDirection)
            ->paginate($perPage);
    }
    public function searchByName(
        string $query,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->with(['category', 'unit', 'user'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('product_code', '=', $query);
            })
            ->orderBy($sortBy,
            $sortDirection)
            ->paginate($perPage);
    }   
}