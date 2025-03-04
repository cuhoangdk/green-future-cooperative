<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Posts\IndexPostRequest;
use App\Http\Requests\Posts\SearchPostRequest;
use App\Http\Requests\Posts\StorePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Traits\GeneratesSlug;
use App\Services\UploadFileService;
class PostController extends Controller
{
    use GeneratesSlug;

    protected $postRepository;
    protected $uploadService;

    public function __construct(PostRepositoryInterface $postRepository, UploadFileService $uploadService)
    {
        $this->postRepository = $postRepository;
        $this->uploadService = $uploadService;
    }
    /**
     * Lấy danh sách bài viết.
     * 
     * @param IndexPostRequest $request - Chứa `per_page`, `sort_by`, `sort_direction`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function index(IndexPostRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'updated_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $posts = $this->postRepository
        ->getAll($sortBy, $sortDirection, $perPage)
        ->appends(request()->query());
        ;

        return PostResource::collection($posts);
    }
    /**
     * Tìm kiếm bài viết theo các tiêu chí lọc.
     * 
     * @param SearchPostRequest $request - Chứa `per_page`, `sort_by`, `sort_direction`, `search`, `user_id`, `category_id`, `status`, `start_date`, `end_date`, `is_hot`, `is_featured`
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function search(SearchPostRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'updated_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $filters = array_filter([
            'search' => $request->input('search'),
            'user' => $request->input('user_id'),
            'category' => $request->input('category_id'),
            'status' => $request->input('status'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'is_hot' => $request->has('is_hot') ? $request->boolean('is_hot') : null,
            'is_featured' => $request->has('is_featured') ? $request->boolean('is_featured') : null,
        ], fn($value) => $value !== null);

        $posts = $this->postRepository->getFilteredPosts($sortBy, $sortDirection, $perPage, $filters)
            ->appends(request()->query());

        return PostResource::collection($posts);
    }

    /**
     * Hiển thị chi tiết bài viết.
     * 
     * @param int $id - ID bài viết cần hiển thị.
     * @return PostResource|\Illuminate\Http\JsonResponse - Thông tin bài viết hoặc thông báo lỗi.
     */
    public function show($id)
    {
        $post = $this->postRepository->getById($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->load(['category', 'user']);
        return new PostResource($post);
    }
    /**
     * Tạo mới một bài viết.
     * 
     * @param StorePostRequest $request - Yêu cầu chứa dữ liệu bài viết bao gồm title, summary, content, featured_image, category_id, user_id, post_status, is_hot, is_featured.
     * @return PostResource - Bài viết vừa tạo.
     */
    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $this->uploadService->uploadImage($request->file('featured_image'), 'posts');
        }
        // Nếu trạng thái không phải 'draft', tự động thêm published_at
        if ($validated['post_status'] !== 'draft') {
            $validated['published_at'] = now();
        }
        $post = $this->postRepository->create($validated);
        return new PostResource($post);
    }
    /**
     * Cập nhật thông tin bài viết.
     *
     * @param UpdatePostRequest $request - Yêu cầu chứa dữ liệu cần cập nhật.
     * @param int $id - ID bài viết cần cập nhật.
     * @return \Illuminate\Http\JsonResponse - Bài viết đã cập nhật hoặc thông báo lỗi.
     */
    public function update(UpdatePostRequest $request, $id)
    {
        // Lấy dữ liệu đã validated từ request
        $validated = $request->validated();

        // Lấy bài viết từ repository
        $post = $this->postRepository->getById($id);

        // Kiểm tra nếu bài viết không tồn tại
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        // Kiểm tra nếu không có dữ liệu nào được gửi để cập nhật
        if (empty($validated) && !$request->hasFile('featured_image')) {
            return response()->json(['message' => 'No valid data provided.'], 422);
        }

        // Xử lý file ảnh nếu có
        if ($request->hasFile('featured_image')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            if (!empty($post->featured_image)) {
                $this->uploadService->deleteImage($post->featured_image);
            }

            // Upload ảnh mới
            $validated['featured_image'] = $this->uploadService->uploadImage($request->file('featured_image'), 'posts');
        }

        // Nếu trạng thái là 'draft', thì đặt `published_at` = null
        if (isset($validated['post_status']) && $validated['post_status'] === 'draft') {
            $validated['published_at'] = null;
        }
        // Nếu trạng thái không phải 'draft' và chưa có `published_at`, đặt thời gian hiện tại
        elseif (isset($validated['post_status']) && $validated['post_status'] !== 'draft' && is_null($post->published_at)) {
            $validated['published_at'] = now();
        }

        // Gọi repository để cập nhật bài viết
        $updatedPost = $this->postRepository->update($id, $validated);

        // Trả về JSON response
        return response()->json([
            'message' => 'Post updated successfully',
            'data' => new PostResource($updatedPost),
        ], 200);
    }
    /**
     * Xóa bài viết.
     * 
     * @param int $id - ID bài viết cần xóa.
     * @return \Illuminate\Http\JsonResponse - Thông báo xóa thành công hoặc lỗi.
     */
    public function destroy($id)
    {
        $deleted = $this->postRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json(['message' => 'Post deleted successfully']);
    }
    /**
     * Lấy danh sách bài viết đã xóa mềm.
     * 
     * @param IndexPostRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function trashed(IndexPostRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'deleted_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $trashedPosts = $this->postRepository->getTrashed(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage
        );

        return PostResource::collection($trashedPosts);
    }

    /**
     * Xem bài viết theo slug.
     * 
     * @param $slug - Slug bài viết cần hiện.
     * @return PostResource - Bài viết chưa slug đó.
     */
    public function getBySlug($slug)
    {
        $post = $this->postRepository->getBySlug($slug);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->load(['category', 'user']);
        return new PostResource($post);
    }
     /**
     * Xem danh sách bài viết theo loại.
     * 
     * @param int $category_id 
     * @param IndexPostRequest $request - chứa `per_page`, `sort_by`, `sort_direction`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function getByCategory(IndexPostRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $categoryId = $request->categoryId;

        $posts = $this->postRepository
            ->getByCategory($categoryId, $perPage, $sortDirection, $sortBy)
            ->appends(request()->query());
        return PostResource::collection($posts);
    }
    /**
     * Khôi phục bài viết.
     * 
     * @param int $id - ID bài viết cần khôi phục.
     * @return \Illuminate\Http\JsonResponse - Thông báo khôi phục thành công hoặc lỗi.
     */
    public function restore($id)
    {
        $post = $this->postRepository->getTrashedById($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found or not trashed'], 404);
        }

        $restored = $this->postRepository->restore($id);

        if (!$restored) {
            return response()->json(['message' => 'Failed to restore post'], 500);
        }

        return response()->json(['message' => 'Post restored successfully']);
    }
    /**
     * Xóa vĩnh viễn bài viết.
     * 
     * @param int $id - ID bài viết cần xóa.
     * @return \Illuminate\Http\JsonResponse - Thông báo xóa thành công hoặc lỗi.
     */
    public function forceDelete($id)
    {
        $post = $this->postRepository->getTrashedById($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found or not trashed'], 404);
        }

        $deleted = $this->postRepository->forceDelete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Failed to permanently delete post'], 500);
        }

        return response()->json(['message' => 'Post permanently deleted successfully']);
    }
    /**
     * Xem bài viết hot.
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function getHotPosts()
    {
        $posts = $this->postRepository->getHotPosts();
        return PostResource::collection($posts);
    }
    /**
     * Xem bài viết nổi bật.
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function getFeaturedPosts()
    {
        $posts = $this->postRepository->getFeaturedPosts();
        return PostResource::collection($posts);
    }
    /**
     * Lấy danh sách bài viết theo slug của danh mục.
     * 
     * @param string $slug - Slug của danh mục.
     * @param IndexPostRequest $request - Yêu cầu chứa `per_page`, `sort_by`, `sort_direction`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function getByCategorySlug($slug, IndexPostRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Tìm danh mục dựa trên slug
        $category = $this->postRepository->getCategoryBySlug($slug);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $posts = $this->postRepository
            ->getByCategory($category->id, $perPage, $sortDirection, $sortBy)
            ->appends(request()->query());

        return PostResource::collection($posts);
    }

}
