<?php

namespace App\Http\Controllers\Api;

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

    public function index()
    {
        $posts = $this->postRepository->getAll();
        return PostResource::collection($posts);
    }

    public function show($id)
    {
        $post = $this->postRepository->getById($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return new PostResource($post);
    }

    public function store(StoreUpdatePostRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);

        $post = $this->postRepository->create($validated);
        return new PostResource($post);
    }

    public function update(StoreUpdatePostRequest $request, $id)
    {
        $validated = $request->validated();

        if (isset($validated['title'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title']);
        }

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
        return new PostResource($post);
    }

    public function getByCategory($categoryId)
    {
        $posts = $this->postRepository->getByCategory($categoryId);
        return PostResource::collection($posts);
    }
}
