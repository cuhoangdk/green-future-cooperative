<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farm\SearchFarmRequest;
use App\Http\Requests\Farm\StoreFarmRequest;
use App\Http\Requests\Farm\UpdateFarmRequest;
use App\Http\Requests\Farm\IndexFarmRequest;
use App\Http\Resources\FarmResource;
use App\Repositories\Contracts\FarmRepositoryInterface;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    protected $farmRepository;

    public function __construct(FarmRepositoryInterface $farmRepository)
    {
        $this->farmRepository = $farmRepository;
    }

    public function index(IndexFarmRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        
        $farms = $this->farmRepository->getAll($sortBy, $sortDirection, $perPage)
        ->appends(request()->query());
        return FarmResource::collection($farms);
    }

    public function store(StoreFarmRequest $request)
    {
        $data = $request->validated();        
        $farm = $this->farmRepository->create($data);
        return new FarmResource($farm);
    }

    public function show($id)
    {
        $farm = $this->farmRepository->getById($id);
        if (!$farm) {
            return response()->json(['message' => 'Farm not found'], 404);
        }
        return new FarmResource($farm);
    }

    public function update(UpdateFarmRequest $request, $id)
    {
        $farm = $this->farmRepository->update($id, $request->validated());
        if (!$farm) {
            return response()->json(['message' => 'Farm not found'], 404);
        }
        return new FarmResource($farm);
    }

    public function destroy($id)
    {
        $deleted = $this->farmRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Farm not found'], 404);
        }
        return response()->json(['message' => 'Farm deleted successfully']);
    }
    /**
     * Lấy danh sách nông trại đã xóa mềm.
     * 
     * @param IndexFarmRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function trashed(IndexFarmRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'deleted_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $trashedUsers = $this->farmRepository->getTrashed(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage
        )->appends(request()->query());

        return FarmResource::collection($trashedUsers);
    }

    /**
     * Khôi phục nông trại đã bị xóa.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $restored = $this->farmRepository->restore($id);

        if (!$restored) {
            return response()->json(['message' => 'Farm not found or not trashed'], 404);
        }

        return response()->json(['message' => 'Farm restored successfully']);
    }

    /**
     * Xóa vĩnh viễn nông trại.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        $deleted = $this->farmRepository->forceDelete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Farm not found or not trashed'], 404);
        }

        return response()->json(['message' => 'Farm permanently deleted successfully']);
    }

    public function search(SearchFarmRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $filters = $request->only(['search', 'user_id']);

        $farms = $this->farmRepository->search($sortBy, $sortDirection, $perPage, $filters)->appends(request()->query());
        return FarmResource::collection($farms);
    }
}
