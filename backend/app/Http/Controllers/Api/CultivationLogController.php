<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CultivationLog\IndexCultivationLogRequest;
use App\Http\Requests\CultivationLog\StoreCultivationLogRequest;
use App\Http\Requests\CultivationLog\UpdateCultivationLogRequest;
use App\Http\Resources\CultivationLogResource;
use App\Repositories\Contracts\CultivationLogRepositoryInterface;
use App\Services\UploadFileService;
use Illuminate\Http\Request;

class CultivationLogController extends Controller
{
    protected $repository;
    protected $uploadFileService;

    public function __construct(
        CultivationLogRepositoryInterface $repository,
        UploadFileService $uploadFileService
    ) {
        $this->repository = $repository;
        $this->uploadFileService = $uploadFileService;
    }

    public function index(IndexCultivationLogRequest $request)
    {
        $productId = (int) $request->route('product_id');
        $logs = $this->repository->getAll(
            $productId,
            $request->get('sort_by', 'created_at'),
            $request->get('sort_direction', 'desc'),
            $request->get('per_page')
        );
        return CultivationLogResource::collection($logs);
    }

    public function store(int $productId, StoreCultivationLogRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $this->uploadFileService->uploadImage(
                $request->file('image_url'),
                'cultivation_logs'
            );
        }

        // Sử dụng product_id từ route, không cần từ body
        $log = $this->repository->create($productId, $data);
        return new CultivationLogResource($log);
    }

    public function show(int $productId, $id)
    {
        $log = $this->repository->getById($productId, $id);
        return new CultivationLogResource($log);
    }

    public function update(int $productId, $id, UpdateCultivationLogRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image_url')) {
            $log = $this->repository->getById($productId, $id);
            if ($log->image_url) {
                $this->uploadFileService->deleteImage($log->image_url);
            }
            $data['image_url'] = $this->uploadFileService->uploadImage(
                $request->file('image_url'),
                'cultivation_logs'
            );
        }

        // Sử dụng product_id từ route, không cần từ body
        $log = $this->repository->update($productId, $id, $data);
        return new CultivationLogResource($log);
    }

    public function destroy(int $productId, $id)
    {
        $log = $this->repository->getById($productId, $id);
        if ($log->image_url) {
            $this->uploadFileService->deleteImage($log->image_url);
        }
        $this->repository->delete($productId, $id);
        return response()->json(['message' => 'Cultivation log deleted successfully']);
    }
}