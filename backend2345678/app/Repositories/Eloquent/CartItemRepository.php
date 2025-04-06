<?php

namespace App\Repositories\Eloquent;

use App\Models\CartItem;
use App\Models\ProductQuantityPrice;
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
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->each(function ($item) {
                $item->calculated_price = $this->getPriceForQuantity($item->product_id, $item->quantity);
            });
    }

    public function getById(int $customerId, $id)
    {
        $item = $this->model->where('customer_id', $customerId)
            ->with([
                'product',
                'product.images' => function ($query) {
                    $query->where('is_primary', true);
                }
            ])
            ->findOrFail($id);

        // Thêm calculated_price
        // $item->calculated_price = $this->getPriceForQuantity($item->product_id, $item->quantity);

        return $item;
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

            // Lấy giá từ product_quantity_prices
            $price = $this->getPriceForQuantity($data['product_id'], $newQuantity);
            if ($price === null) {
                throw new \Exception("No price defined for product ID {$data['product_id']} with quantity {$newQuantity}");
            }

            if ($existingItem) {
                $existingItem->quantity = $newQuantity;
                $existingItem->save();
                $existingItem->calculated_price = $price; // Thêm calculated_price
                return $existingItem;
            }

            $item = $this->model->create($data);
            $item->calculated_price = $price; // Thêm calculated_price
            return $item;
        });
    }

    public function update(int $customerId, $id, array $data)
    {
        return DB::transaction(function () use ($customerId, $id, $data) {
            $item = $this->getById($customerId, $id);
            $product = $item->product;

            if ($product->status !== 'selling') {
                throw new \Exception("Product {$product->name} is not available for sale (current status: {$product->status}).");
            }

            $newQuantity = $data['quantity'] ?? $item->quantity;
            if ($newQuantity > $product->stock_quantity) {
                throw new \Exception("The quantity ({$newQuantity}) exceeds the available stock ({$product->stock_quantity}).");
            }

            $price = $this->getPriceForQuantity($item->product_id, $newQuantity);

            // Cập nhật quantity
            $item->quantity = $newQuantity;
            $item->save(); // Lưu thủ công, chỉ cập nhật các cột trong $fillable

            // Gán calculated_price
            $item->calculated_price = $price;

            return $item;
        });
    }

    public function delete(int $customerId, $id)
    {
        $item = $this->getById($customerId, $id);
        $item->delete();
        return $item;
    }

    /**
     * Lấy giá từ product_quantity_prices dựa trên product_id và quantity
     */
    protected function getPriceForQuantity(int $productId, float $quantity)
    {
        $priceRecordExists = ProductQuantityPrice::where('product_id', $productId)->exists();

        if (!$priceRecordExists) {
            return 'Contact Price'; // Không có bản ghi, trả về "Giá liên hệ"
        }
        // Lấy ngưỡng nhỏ nhất cho product_id
        $minQuantity = ProductQuantityPrice::where('product_id', $productId)
            ->min('quantity');

        // Nếu quantity nhỏ hơn ngưỡng nhỏ nhất, báo lỗi
        if ($minQuantity !== null && $quantity < $minQuantity) {
            throw new \Exception("Quantity {$quantity} for product ID {$productId} is below the minimum allowed quantity ({$minQuantity})");
        }

        $priceRecord = ProductQuantityPrice::where('product_id', $productId)
            ->where('quantity', '<=', $quantity)
            ->orderBy('quantity', 'desc')
            ->first();

        return $priceRecord ? $priceRecord->price : null;
    }
}