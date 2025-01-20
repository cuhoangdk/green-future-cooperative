<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCategoryRequest;
use App\Http\Resources\PostCategoryResource;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;

class PostCategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(PostCategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        $categories = $this->categoryRepository->getAll();
        return PostCategoryResource::collection($categories);
    }

    public function show($id)
    {
        $category = $this->categoryRepository->getById($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return new PostCategoryResource($category);
    }

    public function store(StoreUpdateCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = $this->categoryRepository->create($validated);
        return new PostCategoryResource($category);
    }

    public function update(StoreUpdateCategoryRequest $request, $id)
    {
        $validated = $request->validated();

        $category = $this->categoryRepository->update($id, $validated);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return new PostCategoryResource($category);
    }

    public function destroy($id)
    {
        $deleted = $this->categoryRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json(['message' => 'Category deleted successfully']);
    }

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
