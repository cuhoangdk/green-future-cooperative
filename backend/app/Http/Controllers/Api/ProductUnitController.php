<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductUnit\IndexProductUnitRequest;
use App\Http\Requests\ProductUnit\StoreProductUnitRequest;
use App\Http\Requests\ProductUnit\UpdateProductUnitRequest;
use App\Http\Resources\ProductUnitResource;
use App\Repositories\Contracts\ProductUnitRepositoryInterface;

class ProductUnitController extends Controller
{
    protected $productUnitRepository;

    public function __construct(ProductUnitRepositoryInterface $productUnitRepository)
    {
        $this->productUnitRepository = $productUnitRepository;
    }

    public function index(IndexProductUnitRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        
        $farms = $this->productUnitRepository->getAll($sortBy, $sortDirection, $perPage)
        ->appends(request()->query());
        return ProductUnitResource::collection($farms);
    }

    public function store(StoreProductUnitRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api_users')->id(); // Gán user_id từ người dùng hiện tại
        $farm = $this->productUnitRepository->create($data);
        return new ProductUnitResource($farm);
    }

    public function show($id)
    {
        $farm = $this->productUnitRepository->getById($id);
        if (!$farm) {
            return response()->json(['message' => 'Product unit not found'], 404);
        }
        return new ProductUnitResource($farm);
    }

    public function update(UpdateProductUnitRequest $request, $id)
    {
        $farm = $this->productUnitRepository->update($id, $request->validated());
        if (!$farm) {
            return response()->json(['message' => 'Product unit not found'], 404);
        }
        return new ProductUnitResource($farm);
    }

    public function destroy($id)
    {
        $deleted = $this->productUnitRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Product unit not found'], 404);
        }
        return response()->json(['message' => 'Product unit deleted successfully']);
    }
    /**
     * Lấy danh sách nông trại đã xóa mềm.
     * 
     * @param IndexProductUnitRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function trashed(IndexProductUnitRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'deleted_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $trashedUsers = $this->productUnitRepository->getTrashed(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage
        );

        return ProductUnitResource::collection($trashedUsers);
    }

    /**
     * Khôi phục nông trại đã bị xóa.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $restored = $this->productUnitRepository->restore($id);

        if (!$restored) {
            return response()->json(['message' => 'Product unit not found or not trashed'], 404);
        }

        return response()->json(['message' => 'Product unit restored successfully']);
    }

    /**
     * Xóa vĩnh viễn nông trại.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        $deleted = $this->productUnitRepository->forceDelete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Product unit not found or not trashed'], 404);
        }

        return response()->json(['message' => 'Product unit permanently deleted successfully']);
    }
}
