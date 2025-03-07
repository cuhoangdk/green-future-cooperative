<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItem\StoreCartItemRequest;
use App\Http\Requests\CartItem\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Repositories\Contracts\CartItemRepositoryInterface;

class CartItemController extends Controller
{
    protected $repository;

    public function __construct(CartItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $customerId = auth('api_customers')->id(); // Giả định dùng auth cho customer
        
        $items = $this->repository->getAll($customerId);
        // Kiểm tra từng mục trong giỏ
        $items->getCollection()->each(function ($item) {
            $product = $item->product;
            if ($product->unit && !$product->unit->allow_decimal && floor($item->quantity) != $item->quantity) {
                $item->invalid_quantity = true;
                $item->invalid_message = "The quantity ({$item->quantity}) must be an integer for this product due to updated unit settings.";
            } else {
                $item->invalid_quantity = false;
            }
        });
        return CartItemResource::collection($items);
    }

    public function store(StoreCartItemRequest $request)
    {
        $customerId = auth('api_customers')->id();
        $data = $request->validated();
        $item = $this->repository->create($customerId, $data);
        return new CartItemResource($item);
    }

    public function show($id)
    {
        $customerId = auth('api_customers')->id();
        $item = $this->repository->getById($customerId, $id);
        return new CartItemResource($item);
    }

    public function update(UpdateCartItemRequest $request, $id)
    {
        $customerId = auth('api_customers')->id();
        $data = $request->validated();
        $item = $this->repository->update($customerId, $id, $data);
        return new CartItemResource($item);
    }

    public function destroy($id)
    {
        $customerId = auth('api_customers')->id();
        $this->repository->delete($customerId, $id);
        return response()->json(['message' => 'Cart item deleted successfully']);
    }
}