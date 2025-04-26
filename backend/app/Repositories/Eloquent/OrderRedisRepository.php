<?php

namespace App\Repositories\Eloquent;

use App\Jobs\SendOrderStatusEmail;
use App\Mail\OrderStatusUpdated;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductQuantityPrice;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderRedisRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    protected function sendOrderStatusEmails(Order $order)
    {
        SendOrderStatusEmail::dispatch($order, 'customer')->onQueue('emails');
        SendOrderStatusEmail::dispatch($order, 'seller')->onQueue('emails');
        SendOrderStatusEmail::dispatch($order, 'super_admin')->onQueue('emails');
    }

    public function getAll(?int $customerId = null, string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10)
    {
        return $this->model->when($customerId !== null, function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })->with('items.product.user')->when(!$this->isSuperAdmin(), function ($query) {
            $userId = auth('api_users')->id();
            $query->whereHas('items.product', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        })->with('items.product.user')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }

    public function getById(?int $customerId = null, $id)
    {
        $query = $this->model->when(!$this->isSuperAdmin(), function ($query) {
            $userId = auth('api_users')->id();
            $query->whereHas('items.product', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        })->with('items.product.user');
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }
        return $query->findOrFail($id);
    }

    public function createForCustomer(?int $customerId = null, array $data)
    {
        return DB::transaction(function () use ($customerId, $data) {
            if (!$customerId) {
                if (empty($data['full_name']) || empty($data['phone_number']) || empty($data['province']) || 
                    empty($data['district']) || empty($data['ward']) || empty($data['street_address'])) {
                    throw new \Exception('Customer information (full_name, phone_number, province, district, ward, street_address) is required for guest orders.');
                }
                if (empty($data['items']) || !is_array($data['items'])) {
                    throw new \Exception('Items are required for guest orders.');
                }
            }

            if ($customerId) {
                $cartItems = CartItem::where('customer_id', $customerId)->with('product.user', 'product.unit')->get();
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
                    $price = $this->getPriceForQuantity($item->product_id, $item->quantity);
                    if ($price === null) {
                        throw new \Exception("No price defined for product ID {$item->product_id} with quantity {$item->quantity}");
                    }
                    $item->calculated_price = $price;
                }
                $items = $cartItems;
            } else {
                $items = collect($data['items'])->map(function ($itemData) {
                    $product = Product::with('user', 'unit')->findOrFail($itemData['product_id']);
                    if ($product->status !== 'selling') {
                        throw new \Exception("Product {$product->name} is not available for sale (current status: {$product->status}).");
                    }
                    if ($itemData['quantity'] > $product->stock_quantity) {
                        throw new \Exception("Insufficient stock for product ID {$product->id}. Available: {$product->stock_quantity}, Requested: {$itemData['quantity']}");
                    }
                    $price = $this->getPriceForQuantity($product->id, $itemData['quantity']);
                    if ($price === null) {
                        throw new \Exception("No price defined for product ID {$product->id} with quantity {$itemData['quantity']}");
                    }
                    return (object) [
                        'product_id' => $product->id,
                        'product' => $product,
                        'quantity' => $itemData['quantity'],
                        'calculated_price' => $price,
                    ];
                });
            }

            if ($customerId && isset($data['customer_address_id'])) {
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

            

            $orderData = array_merge($addressData, [                
                'customer_id' => $customerId,
                'status' => 'pending',
                'total_price' => $items->sum(fn($item) => $item->quantity * $item->calculated_price),
                'shipping_fee' => $data['shipping_fee'] ?? 0,
                'final_total_amount' => $items->sum(fn($item) => $item->quantity * $item->calculated_price) + ($data['shipping_fee'] ?? 0),
                'notes' => $data['notes'] ?? null,
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'email' => $customerId ? null : ($data['email'] ?? null),
            ]);

            $order = $this->model->create($orderData);

            foreach ($items as $item) {
                $totalItemPrice = $item->quantity * $item->calculated_price;
                $productSnapshot = [
                    'id' => $item->product->id,
                    'slug' => $item->product->slug,
                    'product_name' => $item->product->name,
                    'user_full_name' => $item->product->user->full_name ?? null,
                    'unit' => $item->product->unit->name ?? null,
                    'price' => $item->calculated_price,
                ];
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_snapshot' => $productSnapshot,
                    'quantity' => $item->quantity,
                    'price' => $item->calculated_price,
                    'total_item_price' => $totalItemPrice,
                ]);
                $item->product->decrement('stock_quantity', $item->quantity);
                $item->product->increment('sold_quantity', $item->quantity);
                if ($customerId) {
                    CartItem::where('customer_id', $customerId)
                        ->where('product_id', $item->product_id)
                        ->delete();
                }
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
                $product = Product::with('user', 'unit')->findOrFail($item['product_id']);
                
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
            

            $orderData = array_merge($addressData, [
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
                $totalItemPrice = $item->quantity * $item->calculated_price; // Tính total_item_price
                $productSnapshot = [
                    'id' => $item->product->id,
                    'slug' => $item->product->slug,
                    'product_name' => $item->product->name,
                    'user_full_name' => $item->product->user->full_name ?? null,
                    'unit' => $item->product->unit->name ?? null,
                    'price' => $item->calculated_price,
                ];
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_snapshot' => $productSnapshot,
                    'quantity' => $item->quantity,
                    'price' => $item->calculated_price,
                    'total_item_price' => $totalItemPrice, // Đảm bảo lưu giá trị đúng
                ]);
                $item->product->decrement('stock_quantity', $item->quantity);
                $item->product->increment('sold_quantity', $item->quantity);
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
        // Nếu trạng thái mới là 'delivered' và trạng thái cũ không phải 'delivered'
        if (isset($data['status']) && $data['status'] === 'delivered' && $oldStatus !== 'delivered') {
            $customer = Customer::find($order->customer_id);
            if ($customer) {
                $customer->increment('total_spending', $order->final_total_amount);
                $customer->increment('total_orders');
            }
        }
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
                $item->product->decrement('sold_quantity', $item->quantity);
            }
            // Tải lại quan hệ sau khi cập nhật
            $order = $order->fresh(['customer', 'items.product.user']);
            $this->sendOrderStatusEmails($order);

            return $order;
        });
    }
    public function search(?int $customerId = null, int $perPage = 10, array $filters = [], string $sortBy = 'created_at', string $sortDirection = 'desc')
    {        
        $query = $this->model->when(!$this->isSuperAdmin(), function ($query) {
            $userId = auth('api_users')->id();
            $query->whereHas('items.product', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        })->with('items.product.user');

        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")               
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }
        if (!empty($filters['month'])) {
            $query->whereMonth('created_at', $filters['month']);
        }
        if (!empty($filters['day'])) {
            $query->whereDay('created_at', $filters['day']);
        }
        $query->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status));

        return $query->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }  
    protected function getPriceForQuantity(string $productId, float $quantity)
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

    protected function isSuperAdmin(): bool
    {
        $user = auth('api_users')->user();
        return $user && $user->is_super_admin;
    }
}