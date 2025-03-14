<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImage\IndexProductImageRequest;
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

    public function index(int $productId, IndexProductImageRequest $request)
    {
        $perPage = $request->input('per_page');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $images = $this->repository->getAll(
            productId: $productId,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage 
        )->appends(request()->query());        
        return ProductImageResource::collection($images);
    }

    public function store(int $productId, StoreProductImageRequest $request)
    {
        $data = $request->validated();
        $images = [];

        // Kiểm tra nếu có nhiều file được upload
        if ($request->hasFile('image_urls')) {
            $uploadedFiles = $request->file('image_urls');
            // Đảm bảo uploadedFiles là mảng
            $uploadedFiles = is_array($uploadedFiles) ? $uploadedFiles : [$uploadedFiles];

            foreach ($uploadedFiles as $file) {
                $imageData = $data; // Copy dữ liệu validated
                $imageData['image_url'] = $this->uploadFileService->uploadImage(
                    $file,
                    'product_images'
                );
                $image = $this->repository->create($productId, $imageData);
                $images[] = $image;
            }
        }

        return ProductImageResource::collection($images);
    }

    public function show(int $productId, $id)
    {
        $image = $this->repository->getById($productId, $id);
        return new ProductImageResource($image);
    }

    // public function update(int $productId, $id, UpdateProductImageRequest $request)
    // {
    //     $data = $request->validated();
        
    //     // Lấy image hiện tại
    //     $image = $this->repository->getById($productId, $id);

    //     // Xử lý khi có file mới được upload
    //     if ($request->hasFile('image_urls')) {
    //         // Xóa ảnh cũ nếu tồn tại
    //         if ($image->image_url) {
    //             $this->uploadFileService->deleteImage($image->image_url);
    //         }
            
    //         $uploadedFiles = $request->file('image_urls');
    //         // Lấy file đầu tiên trong mảng (nếu muốn hỗ trợ nhiều ảnh thì cần tạo record mới)
    //         $file = is_array($uploadedFiles) ? $uploadedFiles[0] : $uploadedFiles;
            
    //         $data['image_url'] = $this->uploadFileService->uploadImage(
    //             $file,
    //             'product_images'
    //         );
    //     }

    //     $updatedImage = $this->repository->update($productId, $id, $data);
    //     return new ProductImageResource($updatedImage);
    // }

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