<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderImage\StoreSliderImageRequest;
use App\Http\Requests\SliderImage\UpdateSliderImageRequest;
use App\Http\Resources\SliderImageResource;
use App\Repositories\Contracts\SliderImageRepositoryInterface;

class SliderImageController extends Controller
{
    protected $repository;

    public function __construct(SliderImageRepositoryInterface $repository)
    {
        $this->repository = $repository;        
    }

    public function index()
    {
        $sliderImages = $this->repository->getAll();
        return SliderImageResource::collection($sliderImages);
    }

    public function store(StoreSliderImageRequest $request)
    {
        $sliderImage = $this->repository->create($request->validated());
        return new SliderImageResource($sliderImage);
    }

    public function show($id)
    {
        $sliderImage = $this->repository->getById($id);
        return new SliderImageResource($sliderImage);
    }

    public function update(UpdateSliderImageRequest $request, $id)
    {
        $sliderImage = $this->repository->update($id, $request->validated());
        return new SliderImageResource($sliderImage);
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(null, 204);
    }
}