<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderImage\StoreSliderImageRequest;
use App\Http\Requests\SliderImage\UpdateSliderImageRequest;
use App\Http\Resources\SliderImageResource;
use App\Repositories\Contracts\SliderImageRepositoryInterface;
use App\Services\UploadFileService;

class SliderImageController extends Controller
{
    protected $repository;
    protected $uploadService;


    public function __construct(SliderImageRepositoryInterface $repository, UploadFileService $uploadService)
    {
        $this->repository = $repository;
        $this->uploadService = $uploadService;

    }

    public function index()
    {
        $sliderImages = $this->repository->getAll();
        return SliderImageResource::collection($sliderImages);
    }

    public function store(StoreSliderImageRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image_url')) {
            $validated['image_url'] = $this->uploadService->uploadImage($request->file('image_url'), 'slides');
        }
        $sliderImage = $this->repository->create($validated);
        return new SliderImageResource($sliderImage);
    }

    public function show($id)
    {
        $sliderImage = $this->repository->getById($id);
        return new SliderImageResource($sliderImage);
    }

    public function update(UpdateSliderImageRequest $request, $id)
    {
        $validated = $request->validated();

        // Lấy bài viết từ repository
        $slide = $this->repository->getById($id);
        // Xử lý file ảnh nếu có
        if ($request->hasFile('image_url')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            if (!empty($post->image_url)) {
                $this->uploadService->deleteImage($slide->image_url);
            }

            // Upload ảnh mới
            $validated['image_url'] = $this->uploadService->uploadImage($request->file('image_url'), 'slides');
        }
        $updatedSlide = $this->repository->update($id, $validated);
        // Trả về JSON response
        return response()->json([
            'message' => 'Post updated successfully',
            'data' => new SliderImageResource($updatedSlide),
        ], 200);    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(null, 204);
    }
}