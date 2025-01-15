<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index()
    {
        $posts = $this->postRepository->getAll();
        return response()->json($posts);
    }

    public function show($id)
    {
        $post = $this->postRepository->getById($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json($post);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:550',
            'content' => 'required',
            'featured_image' => 'nullable|string',
            'category_id' => 'required|exists:post_categories,id',
            'author_id' => 'required|exists:users,id',
            'post_status' => 'in:draft,published,archived'
        ]);

        $post = $this->postRepository->create($validated);
        return response()->json($post, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'summary' => 'nullable|string|max:550',
            'content' => 'nullable',
            'featured_image' => 'nullable|string',
            'category_id' => 'nullable|exists:post_categories,id',
            'author_id' => 'nullable|exists:users,id',
            'post_status' => 'in:draft,published,archived'
        ]);

        $post = $this->postRepository->update($id, $validated);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json($post);
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
        return response()->json($post);
    }

    public function getByCategory($categoryId)
    {
        $posts = $this->postRepository->getByCategory($categoryId);
        return response()->json($posts);
    }
}
