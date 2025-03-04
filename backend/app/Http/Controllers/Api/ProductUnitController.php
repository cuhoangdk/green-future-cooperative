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
        $perPage = $request->input('per_page');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        
        $productUnit = $this->productUnitRepository->getAll($sortBy, $sortDirection, $perPage)
        ->appends(request()->query());
        return ProductUnitResource::collection($productUnit);
    }

    public function store(StoreProductUnitRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api_users')->id(); // Gán user_id từ người dùng hiện tại
        $productUnit = $this->productUnitRepository->create($data);
        return new ProductUnitResource($productUnit);
    }

    public function show($id)
    {
        $productUnit = $this->productUnitRepository->getById($id);
        if (!$productUnit) {
            return response()->json(['message' => 'Product unit not found'], 404);
        }
        return new ProductUnitResource($productUnit);
    }

    public function update(UpdateProductUnitRequest $request, $id)
    {
        $productUnit = $this->productUnitRepository->getById($id);
        if (!$productUnit) {
            return response()->json(['message' => 'Product unit not found'], 404);
        }

        $data = $request->validated();

        // Kiểm tra nếu allow_decimal thay đổi từ true thành false
        if (isset($data['allow_decimal']) && !$data['allow_decimal'] && $productUnit->allow_decimal) {
            $hasDecimal = $productUnit->products()
                ->with(['quantityPrices' => function ($query) {
                    $query->whereRaw('FLOOR(quantity) != quantity');
                }])
                ->whereHas('quantityPrices', function ($query) {
                    $query->whereRaw('FLOOR(quantity) != quantity');
                })
                ->exists();

            if ($hasDecimal) {
                return response()->json([
                    'error' => 'Cannot disable decimal quantities because existing quantity prices for this unit have decimal values.'
                ], 422);
            }
        }

        $updatedProductUnit = $this->productUnitRepository->update($id, $data);
        if (!$updatedProductUnit) {
            return response()->json(['message' => 'Product unit update failed'], 500);
        }

        return new ProductUnitResource($updatedProductUnit);
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
