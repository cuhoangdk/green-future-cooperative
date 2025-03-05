<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImage\StoreProductImageRequest;
use App\Http\Requests\ProductImage\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Repositories\Contracts\ProductImageRepositoryInterface;
use App\Services\UploadFileService;

class ProductImageController extends Controller
{
    protected $repository;
    protected $uploadFileService;

    public function __construct(
        ProductImageRepositoryInterface $repository,
        UploadFileService $uploadFileService
    ) {
        $this->repository = $repository;
        $this->uploadFileService = $uploadFileService;
    }

    public function index(int $productId)
    {
        $images = $this->repository->getAll($productId);
        return ProductImageResource::collection($images);
    }

    public function store(int $productId, StoreProductImageRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $this->uploadFileService->uploadImage(
                $request->file('image_url'),
                'product_images'
            );
        }

        $image = $this->repository->create($productId, $data);
        return new ProductImageResource($image);
    }

    public function show(int $productId, $id)
    {
        $image = $this->repository->getById($productId, $id);
        return new ProductImageResource($image);
    }

    public function update(int $productId, $id, UpdateProductImageRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image_url')) {
            $image = $this->repository->getById($productId, $id);
            if ($image->image_url) {
                $this->uploadFileService->deleteImage($image->image_url);
            }
            $data['image_url'] = $this->uploadFileService->uploadImage(
                $request->file('image_url'),
                'product_images'
            );
        }

        $image = $this->repository->update($productId, $id, $data);
        return new ProductImageResource($image);
    }

    public function destroy(int $productId, $id)
    {
        $image = $this->repository->getById($productId, $id);
        if ($image->image_url) {
            $this->uploadFileService->deleteImage($image->image_url);
        }
        $this->repository->delete($productId, $id);
        return response()->json(['message' => 'Product image deleted successfully']);
    }
}