<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductQuantityPrice;
use App\Repositories\Contracts\ProductQuantityPriceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductQuantityPriceRepository implements ProductQuantityPriceRepositoryInterface
{
    protected $model;

    public function __construct(ProductQuantityPrice $model)
    {
        $this->model = $model;
    }

    public function getAll(string $productId, string $sortBy = 'quantity', string $sortDirection = 'asc', int $perPage = null)
    {        

        $query = $this->model->where('product_id', $productId)->orderBy($sortBy, $sortDirection);
        // Nếu $perPage không được cung cấp, trả về danh sách không phân trang
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getTrashed(string $productId, string $sortBy = 'deleted_at', string $sortDirection = 'desc', int $perPage = 10)
    {
        return $this->model->onlyTrashed()
            ->where('product_id', $productId)
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }

    public function getById(string $productId, $id)
    {
        return $this->model->where('product_id', $productId)
            ->findOrFail($id);
    }

    public function create(string $productId, array $data)
    {
        return DB::transaction(function () use ($productId, $data) {
            $data['product_id'] = $productId;
            return $this->model->create($data);
        });
    }

    public function update(string $productId, $id, array $data)
    {
        return DB::transaction(function () use ($productId, $id, $data) {
            $price = $this->getById($productId, $id);
            $price->update($data);
            return $price;
        });
    }

    public function delete(string $productId, $id)
    {
        $price = $this->getById($productId, $id);
        $price->delete();
        return $price;
    }

    public function restore(string $productId, $id)
    {
        $price = $this->model->onlyTrashed()
            ->where('product_id', $productId)
            ->findOrFail($id);
        $price->restore();
        return $price;
    }

    public function forceDelete(string $productId, $id)
    {
        $price = $this->model->onlyTrashed()
            ->where('product_id', $productId)
            ->findOrFail($id);
        $price->forceDelete();
        return true;
    }
}