<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductImage;
use App\Repositories\Contracts\ProductImageRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductImageRepository implements ProductImageRepositoryInterface
{
    protected $model;

    public function __construct(ProductImage $model)
    {
        $this->model = $model;
    }

    public function getAll(int $productId, string $sortBy = 'sort_order', string $sortDirection = 'asc', int $perPage = null)
    {
        $query = $this->model->where('product_id', $productId)->orderBy($sortBy, $sortDirection);

        // Nếu $perPage không được cung cấp, trả về danh sách không phân trang
        return $perPage ? $query->paginate($perPage) : $query->get();        
    }

    public function getById(int $productId, $id)
    {
        return $this->model->where('product_id', $productId)
            ->findOrFail($id);
    }

    public function create(int $productId, array $data)
    {
        return DB::transaction(function () use ($productId, $data) {
            $data['product_id'] = $productId;
            return $this->model->create($data);
        });
    }

    public function update(int $productId, $id, array $data)
    {
        return DB::transaction(function () use ($productId, $id, $data) {
            $image = $this->getById($productId, $id);
            $image->update($data);
            return $image;
        });
    }

    public function delete(int $productId, $id)
    {
        $image = $this->getById($productId, $id);
        $image->delete();
        return $image;
    }
}