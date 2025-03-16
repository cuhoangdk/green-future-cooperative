<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductQuantityPrice\IndexProductQuantityPriceRequest;
use App\Http\Requests\ProductQuantityPrice\StoreProductQuantityPriceRequest;
use App\Http\Requests\ProductQuantityPrice\UpdateProductQuantityPriceRequest;
use App\Http\Resources\ProductQuantityPriceResource;
use App\Repositories\Contracts\ProductQuantityPriceRepositoryInterface;

class ProductQuantityPriceController extends Controller
{
    protected $repository;

    public function __construct(ProductQuantityPriceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(int $productId, IndexProductQuantityPriceRequest $request)
    {
        $prices = $this->repository->getAll(
            $productId,
            $request->get('sort_by', 'quantity'),
            $request->get('sort_direction', 'asc'),
            $request->get('per_page')
        )->appends(request()->query());
        return ProductQuantityPriceResource::collection($prices);
    }

    public function store(int $productId, StoreProductQuantityPriceRequest $request)
    {
        $data = $request->validated();
        $prices = [];

        // Xử lý mảng các đối tượng prices
        foreach ($data['prices'] as $priceData) {
            // Tạo dữ liệu để lưu vào repository
            $priceRecord = [
                'quantity' => $priceData['quantity'],
                'price' => $priceData['price'],
            ];

            // Lưu vào repository
            $price = $this->repository->create($productId, $priceRecord);
            $prices[] = $price;
        }        
        return new ProductQuantityPriceResource($prices);
    }

    public function show(int $productId, $id)
    {
        $price = $this->repository->getById($productId, $id);
        return new ProductQuantityPriceResource($price);
    }

    public function update(int $productId, $id, UpdateProductQuantityPriceRequest $request)
    {
        $data = $request->validated();
        $price = $this->repository->update($productId, $id, $data);
        return new ProductQuantityPriceResource($price);
    }

    public function destroy(int $productId, $id)
    {
        $this->repository->delete($productId, $id);
        return response()->json(['message' => 'Product quantity price deleted successfully']);
    }

    public function trashed(int $productId, IndexProductQuantityPriceRequest $request)
    {
        $prices = $this->repository->getTrashed(
            $productId,
            $request->get('sort_by', 'deleted_at'),
            $request->get('sort_direction', 'desc'),
            $request->get('per_page',10)
        )->appends(request()->query());
        return ProductQuantityPriceResource::collection($prices);
    }

    public function restore(int $productId, $id)
    {
        $price = $this->repository->restore($productId, $id);
        return new ProductQuantityPriceResource($price);
    }

    public function forceDelete(int $productId, $id)
    {
        $this->repository->forceDelete($productId, $id);
        return response()->json(['message' => 'Product quantity price permanently deleted']);
    }
}