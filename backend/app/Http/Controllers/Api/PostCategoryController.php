<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PostCategories\IndexCategoryRequest;
use App\Http\Requests\PostCategories\SearchCategoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostCategories\StoreUpdateCategoryRequest;
use App\Http\Resources\PostCategoryResource;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;

class PostCategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(PostCategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * Lấy danh sách loại bài viết.
     * 
     * @param IndexCategoryRequest $request - Chứa `per_page`, `sort_by`, `sort_direction`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function index(IndexCategoryRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $categories = $this->categoryRepository->getAll(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage
        )->appends(request()->query());

        return PostCategoryResource::collection($categories);
    }
     /**
     * Tìm danh sách loại bài viết.
     * 
     * @param SearchCategoryRequest $request - Chứa `search`,`per_page`, `sort_by`, `sort_direction`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function search(SearchCategoryRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $filters = ['search' => $request->input('search')];

        $categories = $this->categoryRepository->getFilteredCategories(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage,
            filters: $filters
        )->appends(request()->query());

        return PostCategoryResource::collection($categories);
    }
    /**
     * Hiển thị chi tiết loại bài viết.
     * 
     * @param int $id - ID bài viết cần hiển thị.
     * @return PostCategoryResource|\Illuminate\Http\JsonResponse - Thông tin loại bài viết hoặc thông báo lỗi.
     */
    public function show($id)
    {
        $category = $this->categoryRepository->getById($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return new PostCategoryResource($category);
    }
    /**
     * Tạo mới một loại bài viết.
     * 
     * @param StoreUpdateCategoryRequest $request - Yêu cầu chứa dữ liệu bài viết bao gồm title, description
     * @return PostCategoryResource - Loại bài viết vừa tạo.
     */
    public function store(StoreUpdateCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = $this->categoryRepository->create($validated);
        return new PostCategoryResource($category);
    }
    /**
     * Cập nhật thông tin loại bài viết.
     * 
     * @param StoreUpdateCategoryRequest $request - Yêu cầu chứa dữ liệu cần cập nhật bao gồm title, description.
     * @param int $id - ID bài viết cần cập nhật.
     * @return PostCategoryResource|\Illuminate\Http\JsonResponse - Loại bài viết đã cập nhật hoặc thông báo lỗi.
     */
    public function update(StoreUpdateCategoryRequest $request, $id)
    {
        $validated = $request->validated();

        $category = $this->categoryRepository->update($id, $validated);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return new PostCategoryResource($category);
    }
    /**
     * Xóa loại bài viết.
     * 
     * @param int $id - ID loại bài viết cần xóa.
     * @return \Illuminate\Http\JsonResponse - Thông báo xóa thành công hoặc lỗi.
     */
    public function destroy($id)
    {
        $deleted = $this->categoryRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json(['message' => 'Category deleted successfully']);
    }
    /**
     * Khôi phục loại bài viết.
     * 
     * @param int $id - ID bài viết cần khôi phục.
     * @return \Illuminate\Http\JsonResponse - Thông báo khôi phục thành công hoặc lỗi.
     */
    public function restore($id)
    {
        $category = $this->categoryRepository->getTrashedById($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found or not trashed'], 404);
        }

        $restored = $this->categoryRepository->restore($id);
        if (!$restored) {
            return response()->json(['message' => 'Failed to restore category'], 500);
        }

        return response()->json(['message' => 'Category restored successfully']);
    }
    /**
     * Xóa vĩnh viễn loại bài viết.
     * 
     * @param int $id - ID loại bài viết cần xóa.
     * @return \Illuminate\Http\JsonResponse - Thông báo xóa thành công hoặc lỗi.
     */
    public function forceDelete($id)
    {
        $category = $this->categoryRepository->getTrashedById($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found or not trashed'], 404);
        }

        $deleted = $this->categoryRepository->forceDelete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Failed to permanently delete category'], 500);
        }

        return response()->json(['message' => 'Category permanently deleted successfully']);
    }
}
