<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductQuantityPriceResource;
use App\Repositories\Contracts\ProductQuantityPriceRepositoryInterface;
use App\Http\Requests\ProductQuantityPrice\IndexProductQuantityPriceRequest;
use App\Http\Requests\ProductQuantityPrice\StoreProductQuantityPriceRequest;
use App\Http\Requests\ProductQuantityPrice\UpdateProductQuantityPriceRequest;

class ProductQuantityPriceController extends Controller
{
    protected $repository;

    public function __construct(ProductQuantityPriceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(string $productId, IndexProductQuantityPriceRequest $request)
    {
        $prices = $this->repository->getAll(
            $productId,
            $request->get('sort_by', 'quantity'),
            $request->get('sort_direction', 'asc'),
            $request->get('per_page')
        );

        // Kiểm tra nếu kết quả là phân trang, rồi mới thêm appends
        if ($prices instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $prices->appends(request()->query());
        }

        return ProductQuantityPriceResource::collection($prices);
    }


    public function store(string $productId, StoreProductQuantityPriceRequest $request)
    {        
        $data = $request->validated();
        $prices = [];

        foreach ($data['prices'] as $priceData) {
            $priceRecord = [
                'quantity' => $priceData['quantity'],
                'price' => $priceData['price'],
            ];
            $price = $this->repository->create($productId, $priceRecord); // Trả về model instance
            $prices[] = $price;
        }

        return ProductQuantityPriceResource::collection($prices); // $prices là mảng các model
    }

    public function show(string $productId, $id)
    {
        $price = $this->repository->getById($productId, $id);
        return new ProductQuantityPriceResource($price);
    }

    public function update(string $productId, $id, UpdateProductQuantityPriceRequest $request)
    {
        $data = $request->validated();
        $price = $this->repository->update($productId, $id, $data);
        return new ProductQuantityPriceResource($price);
    }

    public function destroy(string $productId, $id)
    {
        $this->repository->delete($productId, $id);
        return response()->json(['message' => 'Product quantity price deleted successfully']);
    }

    public function trashed(string $productId, IndexProductQuantityPriceRequest $request)
    {
        $prices = $this->repository->getTrashed(
            $productId,
            $request->get('sort_by', 'deleted_at'),
            $request->get('sort_direction', 'desc'),
            $request->get('per_page',10)
        )->appends(request()->query());
        return ProductQuantityPriceResource::collection($prices);
    }

    public function restore(string $productId, $id)
    {
        $price = $this->repository->restore($productId, $id);
        return new ProductQuantityPriceResource($price);
    }

    public function forceDelete(string $productId, $id)
    {
        $this->repository->forceDelete($productId, $id);
        return response()->json(['message' => 'Product quantity price permanently deleted']);
    }
}