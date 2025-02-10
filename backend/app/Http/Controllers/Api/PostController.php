<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Http\Requests\StoreUpdatePostRequest;
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

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $posts = $this->postRepository->getAll($sortBy, $sortDirection, $perPage);

        return PostResource::collection($posts);
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $filters = [
            'search' => $request->input('search'),
            'author' => $request->input('author_id'),
            'category' => $request->input('category_id'),
            'status' => $request->input('status'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'is_hot' => $request->boolean('is_hot'),
            'is_featured' => $request->boolean('is_featured'),
        ];

        $posts = $this->postRepository->getFilteredPosts($sortBy, $sortDirection, $perPage, $filters)->appends(request()->query());

        return PostResource::collection($posts);
    }


    public function show($id)
    {
        $post = $this->postRepository->getById($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->load(['category', 'author']);
        return new PostResource($post);
    }

    public function store(StoreUpdatePostRequest $request)
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

    public function update(StoreUpdatePostRequest $request, $id)
    {
        $validated = $request->validated();
        $post = $this->postRepository->getById($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($request->hasFile('featured_image')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            $this->uploadService->deleteImage($post->featured_image);

            // Upload ảnh mới
            $validated['featured_image'] = $this->uploadService->uploadImage($request->file('featured_image'), 'posts');
        }
        // Nếu post_status chuyển thành 'draft', thì đặt published_at = null
        if ($validated['post_status'] === 'draft') {
            $validated['published_at'] = null;
        }
        // Nếu post_status không phải 'draft' và chưa có published_at, đặt thời gian hiện tại
        elseif (is_null($post->published_at)) {
            $validated['published_at'] = now();
        }
        $post->update($validated);
        return new PostResource($post);
    }

    public function destroy($id)
    {
        $deleted = $this->postRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function getBySlug($slug)
    {
        $post = $this->postRepository->getBySlug($slug);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->load(['category', 'author']);
        return new PostResource($post);
    }

    public function getByCategory(Request $request)
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

    public function getHotPosts()
    {
        $posts = $this->postRepository->getHotPosts();
        return PostResource::collection($posts);
    }

    public function getFeaturedPosts()
    {
        $posts = $this->postRepository->getFeaturedPosts();
        return PostResource::collection($posts);
    }
}
