<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategory\IndexProductCategoryRequest;
use App\Http\Requests\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\ProductCategory\UpdateProductCategoryRequest;
use App\Http\Resources\ProductCategoryResource;
use App\Repositories\Contracts\ProductCatogoryRepositoryInterface;

class ProductCategoryController extends Controller
{
    protected $productCategoryRepository;

    public function __construct(ProductCatogoryRepositoryInterface $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }
    /**
     * Lấy danh sách loại bài viết.
     * 
     * @param IndexProductCategoryRequest $request - Chứa `per_page`, `sort_by`, `sort_direction`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function index(IndexProductCategoryRequest $request)
    {
        $perPage = $request->input('per_page'); // Bỏ giá trị mặc định
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Gọi repository để lấy dữ liệu (có hoặc không phân trang)
        $productCategory = $this->productCategoryRepository->getAll(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage // Nếu null, repository sẽ trả về danh sách không phân trang
        )->appends(request()->query());

        return ProductCategoryResource::collection($productCategory);
    }
    /**
     * Hiển thị chi tiết loại bài viết.
     * 
     * @param int $id - ID bài viết cần hiển thị.
     * @return ProductCategoryResource|\Illuminate\Http\JsonResponse - Thông tin loại bài viết hoặc thông báo lỗi.
     */
    public function show($id)
    {
        $productCategory = $this->productCategoryRepository->getById($id);

        if (!$productCategory) {
            return response()->json(['message' => 'Product category not found'], 404);
        }

        return new ProductCategoryResource($productCategory);
    }
    /**
     * Tạo mới một loại bài viết.
     * 
     * @param StoreProductCategoryRequest $request - Yêu cầu chứa dữ liệu bài viết bao gồm title, description
     * @return ProductCategoryResource - Loại bài viết vừa tạo.
     */
    public function store(StoreProductCategoryRequest $request)
    {
        $validated = $request->validated();
        $productCategory = $this->productCategoryRepository->create($validated);
        return new ProductCategoryResource($productCategory);
    }
    /**
     * Cập nhật thông tin loại bài viết.
     * 
     * @param UpdateProductCategoryRequest $request - Yêu cầu chứa dữ liệu cần cập nhật bao gồm title, description.
     * @param int $id - ID bài viết cần cập nhật.
     * @return ProductCategoryResource|\Illuminate\Http\JsonResponse - Loại bài viết đã cập nhật hoặc thông báo lỗi.
     */
    public function update(UpdateProductCategoryRequest $request, $id)
    {
        $validated = $request->validated();

        $productCategory = $this->productCategoryRepository->update($id, $validated);
        if (!$productCategory) {
            return response()->json(['message' => 'Product category not found'], 404);
        }

        return new ProductCategoryResource($productCategory);
    }
    /**
     * Xóa loại bài viết.
     * 
     * @param int $id - ID loại bài viết cần xóa.
     * @return \Illuminate\Http\JsonResponse - Thông báo xóa thành công hoặc lỗi.
     */
    public function destroy($id)
    {
        $deleted = $this->productCategoryRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Product category not found'], 404);
        }
        return response()->json(['message' => 'Product category deleted successfully']);
    }
    /**
     * Lấy danh sách các loại bài viết đã xóa mềm.
     * 
     * @param IndexProductCategoryRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function trashed(IndexProductCategoryRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'deleted_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $trashedCategories = $this->productCategoryRepository->getTrashed(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage
        )->appends(request()->query());

        return ProductCategoryResource::collection($trashedCategories);
    }

    /**
     * Khôi phục loại bài viết.
     * 
     * @param int $id - ID bài viết cần khôi phục.
     * @return \Illuminate\Http\JsonResponse - Thông báo khôi phục thành công hoặc lỗi.
     */
    public function restore($id)
    {
        $category = $this->productCategoryRepository->getTrashedById($id);

        if (!$category) {
            return response()->json(['message' => 'Product category not found or not trashed'], 404);
        }

        $restored = $this->productCategoryRepository->restore($id);
        if (!$restored) {
            return response()->json(['message' => 'Failed to restore product category'], 500);
        }

        return response()->json(['message' => 'Product category restored successfully']);
    }
    /**
     * Xóa vĩnh viễn loại bài viết.
     * 
     * @param int $id - ID loại bài viết cần xóa.
     * @return \Illuminate\Http\JsonResponse - Thông báo xóa thành công hoặc lỗi.
     */
    public function forceDelete($id)
    {
        $productCategory = $this->productCategoryRepository->getTrashedById($id);

        if (!$productCategory) {
            return response()->json(['message' => 'Product category not found or not trashed'], 404);
        }

        $deleted = $this->productCategoryRepository->forceDelete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Failed to permanently delete product category'], 500);
        }

        return response()->json(['message' => 'Product category permanently deleted successfully']);
    }
    /**
     * Lấy loại sản phẩm bằng slug
     * @param mixed $slug
     * @return mixed|ProductCategoryResource|\Illuminate\Http\JsonResponse
     */
    public function getBySlug($slug)
    {
        $productCategory = $this->productCategoryRepository->getBySlug($slug);

        if (!$productCategory) {
            return response()->json(['message' => 'Product category not found'], 404);
        }
        return new ProductCategoryResource($productCategory);
    }
}
