<?php

namespace App\Repositories\Eloquent;

use App\Mail\OrderStatusUpdated;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductQuantityPrice;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    protected function sendOrderStatusEmails(Order $order)
    {
        // Người mua (customer)
        if ($order->customer && $order->customer->email) {
            Mail::to($order->customer->email)->queue(new OrderStatusUpdated($order, 'customer'));
        }

        // Người bán (seller) - Lấy từ user_id trong products
        $sellers = $order->items->map(function ($item) {
            return $item->product->user->email ?? null;
        })->filter()->unique();
        foreach ($sellers as $sellerEmail) {
            Mail::to($sellerEmail)->queue(new OrderStatusUpdated($order, 'seller'));
        }

        // Super Admin
        if ($superAdminEmail = config('mail.super_admin_email')) {
            Mail::to($superAdminEmail)->queue(new OrderStatusUpdated($order, 'super_admin'));
        }
    }

    public function getAll(?int $customerId = null, int $perPage = 10)
    {
        $query = $this->model->with('items');
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }
        return $query->paginate($perPage);
    }

    public function getById(?int $customerId = null, $id)
    {
        $query = $this->model->with('items');
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }
        return $query->findOrFail($id);
    }

    public function createForCustomer(int $customerId, array $data)
    {
        return DB::transaction(function () use ($customerId, $data) {
            $cartItems = CartItem::where('customer_id', $customerId)->with('product')->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            foreach ($cartItems as $item) {
                if ($item->product->status !== 'selling') {
                    throw new \Exception("Product {$item->product->name} is not available for sale (current status: {$item->product->status}).");
                }
                if ($item->quantity > $item->product->stock_quantity) {
                    throw new \Exception("Insufficient stock for product ID {$item->product_id}. Available: {$item->product->stock_quantity}, Requested: {$item->quantity}");
                }
                // Lấy giá từ product_quantity_prices dựa trên quantity
                $price = $this->getPriceForQuantity($item->product_id, $item->quantity);
                if ($price === null) {
                    throw new \Exception("No price defined for product ID {$item->product_id} with quantity {$item->quantity}");
                }
                $item->calculated_price = $price; // Lưu giá tạm thời vào item để dùng sau
            }

            if (isset($data['customer_address_id'])) {
                $customerAddress = CustomerAddress::where('customer_id', $customerId)
                    ->with('address')
                    ->findOrFail($data['customer_address_id']);
                
                $addressData = [
                    'full_name' => $customerAddress->full_name,
                    'phone_number' => $customerAddress->phone_number,
                    'address_type' => $customerAddress->address_type,
                    'province' => $customerAddress->address->province ?? '',
                    'district' => $customerAddress->address->district ?? '',
                    'ward' => $customerAddress->address->ward ?? '',
                    'street_address' => $customerAddress->address->street_address ?? '',
                ];
            } else {
                $addressData = [
                    'full_name' => $data['full_name'],
                    'phone_number' => $data['phone_number'],
                    'address_type' => $data['address_type'] ?? 'home',
                    'province' => $data['province'],
                    'district' => $data['district'],
                    'ward' => $data['ward'],
                    'street_address' => $data['street_address'],
                ];
            }

            $timestamp = now()->format('YmdHis');
            $randomSequence = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $orderCode = 'ORD' . $timestamp . $randomSequence;

            $orderData = array_merge($addressData, [
                'order_code' => $orderCode,
                'customer_id' => $customerId,
                'status' => 'pending',
                'total_price' => $cartItems->sum(fn($item) => $item->quantity * $item->calculated_price),
                'shipping_fee' => $data['shipping_fee'] ?? 0,
                'final_total_amount' => $cartItems->sum(fn($item) => $item->quantity * $item->calculated_price) + ($data['shipping_fee'] ?? 0),
                'notes' => $data['notes'] ?? null,
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
            ]);

            $order = $this->model->create($orderData);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_snapshot' => $item->product->toArray(),
                    'quantity' => $item->quantity,
                    'price' => $item->product->calculated_price,
                    'total_item_price' => $item->quantity * $item->product->calculated_price,
                ]);
                $item->product->decrement('stock_quantity', $item->quantity);
                $item->delete();
            }

            $order->load('customer', 'items.product.user');
            $this->sendOrderStatusEmails($order);

            return $order;
        });
    }

    public function createForAdmin(int $customerId, array $data)
    {
        return DB::transaction(function () use ($customerId, $data) {
            $items = collect($data['items'])->map(function ($item) {
                $product = Product::with('unit')->findOrFail($item['product_id']);
                
                if ($product->status !== 'selling') {
                    throw new \Exception("Product {$product->name} is not available for sale (current status: {$product->status}).");
                }

                if ($product->unit && !$product->unit->allow_decimal && floor($item['quantity']) != $item['quantity']) {
                    throw new \Exception("Quantity for product ID {$product->id} must be an integer (e.g., 1, 2, etc.).");
                }

                if ($item['quantity'] > $product->stock_quantity) {
                    throw new \Exception("Insufficient stock for product ID {$product->id}. Available: {$product->stock_quantity}, Requested: {$item['quantity']}");
                }

                $price = $this->getPriceForQuantity($product->id, $item['quantity']);
                if ($price === null) {
                    throw new \Exception("No price defined for product ID {$product->id} with quantity {$item['quantity']}");
                }

                return (object) [
                    'product_id' => $product->id,
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'calculated_price' => $price,
                ];
            });

            if (isset($data['customer_address_id'])) {
                $customerAddress = CustomerAddress::where('customer_id', $customerId)
                    ->with('address')
                    ->findOrFail($data['customer_address_id']);
                
                $addressData = [
                    'full_name' => $customerAddress->full_name,
                    'phone_number' => $customerAddress->phone_number,
                    'address_type' => $customerAddress->address_type,
                    'province' => $customerAddress->address->province ?? '',
                    'district' => $customerAddress->address->district ?? '',
                    'ward' => $customerAddress->address->ward ?? '',
                    'street_address' => $customerAddress->address->street_address ?? '',
                ];
            } else {
                $addressData = [
                    'full_name' => $data['full_name'],
                    'phone_number' => $data['phone_number'],
                    'address_type' => $data['address_type'] ?? 'home',
                    'province' => $data['province'],
                    'district' => $data['district'],
                    'ward' => $data['ward'],
                    'street_address' => $data['street_address'],
                ];
            }

            $timestamp = now()->format('YmdHis');
            $randomSequence = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $orderCode = 'ORD' . $timestamp . $randomSequence;

            $orderData = array_merge($addressData, [
                'order_code' => $orderCode,
                'customer_id' => $customerId,
                'status' => 'pending',
                'total_price' => $items->sum(fn($item) => $item->quantity * $item->calculated_price),
                'shipping_fee' => $data['shipping_fee'] ?? 0,
                'final_total_amount' => $items->sum(fn($item) => $item->quantity * $item->calculated_price) + ($data['shipping_fee'] ?? 0),
                'notes' => $data['notes'] ?? null,
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
            ]);

            $order = $this->model->create($orderData);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_snapshot' => $item->product->toArray(),
                    'quantity' => $item->quantity,
                    'price' => $item->product->calculated_price,
                    'total_item_price' => $item->quantity * $item->product->calculated_price,
                ]);
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            $order->load('customer', 'items.product.user');
            $this->sendOrderStatusEmails($order);

            return $order;
        });
    }

    public function update(?int $customerId = null, $id, array $data)
    {
        $order = $this->getById($customerId, $id);
        $oldStatus = $order->status;

        $order->update($data);

        if (isset($data['status']) && $data['status'] !== $oldStatus && in_array($data['status'], ['pending', 'processing', 'delivered', 'cancelled'])) {
            $order->load('customer', 'items.product.user');
            if ($order instanceof Order) {
                $this->sendOrderStatusEmails($order);
            }
        }

        return $order;
    }

    public function cancel(?int $customerId = null, $id, array $data)
    {
        return DB::transaction(function () use ($customerId, $id, $data) {
            $order = $this->getById($customerId, $id);
            if ($order->status === 'cancelled') {
                throw new \Exception('Order is already cancelled');
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_reason' => $data['cancelled_reason'] ?? null,
                'cancelled_at' => now(),
                'cancelled_by' => auth('api_users')->id() ?? auth('api_customers')->id(),
            ]);

            foreach ($order->items as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }

            $order->load('customer', 'items.product.user');
            $this->sendOrderStatusEmails($order);

            return $order;
        });
    }    
    protected function getPriceForQuantity(int $productId, float $quantity)
    {
        // Lấy ngưỡng nhỏ nhất cho product_id
        $minQuantity = ProductQuantityPrice::where('product_id', $productId)
            ->min('quantity');

        // Nếu quantity nhỏ hơn ngưỡng nhỏ nhất, báo lỗi
        if ($minQuantity !== null && $quantity < $minQuantity) {
            throw new \Exception("Quantity {$quantity} for product ID {$productId} is below the minimum allowed quantity ({$minQuantity})");
        }
        $priceRecord = ProductQuantityPrice::where('product_id', $productId)
            ->where('quantity', '<=', $quantity) // Lấy ngưỡng nhỏ hơn hoặc bằng quantity
            ->orderBy('quantity', 'desc') // Sắp xếp giảm dần để lấy ngưỡng cao nhất phù hợp
            ->first();

        return $priceRecord ? $priceRecord->price : null;
    }
}