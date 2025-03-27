<?php

namespace App\Repositories\Eloquent;

use App\Models\CartItem;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CartItemRepository implements CartItemRepositoryInterface
{
    protected $model;

    public function __construct(CartItem $model)
    {
        $this->model = $model;
    }

    public function getAll(int $customerId)
    {
        return $this->model->where('customer_id', $customerId)
        ->with([
            'product',
            'product.images' => function ($query) {
                $query->where('is_primary', true);
            }
        ])->get();
    }

    public function getById(int $customerId, $id)
    {
        return $this->model->where('customer_id', $customerId)
        ->with([
            'product',
            'product.images' => function ($query) {
                $query->where('is_primary', true);
            }
        ])->findOrFail($id);
    }

    public function create(int $customerId, array $data)
    {
        return DB::transaction(function () use ($customerId, $data) {
            $data['customer_id'] = $customerId;

            $product = \App\Models\Product::findOrFail($data['product_id']);
            
            // Kiểm tra status của sản phẩm
            if ($product->status !== 'selling') {
                throw new \Exception("Product {$product->name} is not available for sale (current status: {$product->status}).");
            }

            $existingItem = $this->model->where('customer_id', $customerId)
                ->where('product_id', $data['product_id'])
                ->first();

            $currentQuantity = $existingItem ? $existingItem->quantity : 0;
            $newQuantity = $currentQuantity + $data['quantity'];

            if ($newQuantity > $product->stock_quantity) {
                throw new \Exception("The total quantity ({$newQuantity}) exceeds the available stock ({$product->stock_quantity}).");
            }

            if ($existingItem) {
                $existingItem->quantity = $newQuantity;
                $existingItem->save();
                return $existingItem;
            }

            return $this->model->create($data);
        });
    }

    public function update(int $customerId, $id, array $data)
    {
        return DB::transaction(function () use ($customerId, $id, $data) {
            $item = $this->getById($customerId, $id);
            $product = $item->product;

            // Kiểm tra status của sản phẩm
            if ($product->status !== 'selling') {
                throw new \Exception("Product {$product->name} is not available for sale (current status: {$product->status}).");
            }

            if (isset($data['quantity']) && $data['quantity'] > $product->stock_quantity) {
                throw new \Exception("The quantity ({$data['quantity']}) exceeds the available stock ({$product->stock_quantity}).");
            }

            $item->update($data);
            return $item;
        });
    }

    public function delete(int $customerId, $id)
    {
        $item = $this->getById($customerId, $id);
        $item->delete();
        return $item;
    }
}