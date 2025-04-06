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
            if (isset($data['is_primary']) && $data['is_primary']) {
                $this->model->where('product_id', $productId)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
            return $this->model->create($data);
        });
    }

    public function update(int $productId, $id, array $data)
    {
        return DB::transaction(function () use ($productId, $id, $data) {
            $image = $this->getById($productId, $id);
            if (isset($data['is_primary']) && $data['is_primary'] && !$image->is_primary) {
                $this->model->where('product_id', $productId)
                    ->where('is_primary', true)
                    ->where('id', '!=', $id)
                    ->update(['is_primary' => false]);
            }
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