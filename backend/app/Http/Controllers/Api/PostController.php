<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource; 
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Http\Requests\StoreUpdatePostRequest;
use App\Traits\GeneratesSlug;

class PostController extends Controller
{
    use GeneratesSlug; 
    
    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index(Request $request)
    {
        // Xử lý các tham số
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $search = $request->input('search');

        // Lấy bài viết với các tham số
        $posts = $this->postRepository->getFilteredPosts(
            search: $search,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage
        );

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

        $post = $this->postRepository->create($validated);
        return new PostResource($post);
    }

    public function update(StoreUpdatePostRequest $request, $id)
    {
        $validated = $request->validated();

        $post = $this->postRepository->update($id, $validated);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

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

    public function getByCategory($categoryId)
    {
        $posts = $this->postRepository->getByCategory($categoryId);
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
}
