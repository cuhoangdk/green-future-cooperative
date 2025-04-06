<?php

namespace App\Repositories\Eloquent;

use App\Models\CultivationLog;
use App\Repositories\Contracts\CultivationLogRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CultivationLogRepository implements CultivationLogRepositoryInterface
{
    protected $model;

    public function __construct(CultivationLog $model)
    {
        $this->model = $model;
    }

    public function getAll(
        int $productId,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = null
    ) {
        $query = $this->model
        // ->with('product')
        ->where('product_id', $productId)->orderBy($sortBy, $sortDirection);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getById(int $productId, $id)
    {
        return $this->model->with('product')
            ->where('product_id', $productId)
            ->findOrFail($id);
    }

    public function create(int $productId, array $data)
    {
        return DB::transaction(function () use ($productId, $data) {
            $data['product_id'] = $productId; // Gán product_id từ tham số
            return $this->model->create($data);
        });
    }

    public function update(int $productId, $id, array $data)
    {
        return DB::transaction(function () use ($productId, $id, $data) {
            $log = $this->getById($productId, $id); // Kiểm tra product_id
            $log->update($data);
            return $log;
        });
    }

    public function delete(int $productId, $id)
    {
        $log = $this->getById($productId, $id); // Kiểm tra product_id
        $log->delete();
        return $log;
    }
}