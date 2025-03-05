<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        return FarmResource::collection($farms->load('address', 'user'));
    }

    public function store(StoreFarmRequest $request)
    {
        $data = $request->validated();        
        $farm = $this->farmRepository->create($data);
        return new FarmResource($farm->load('address', 'user'));
    }

    public function show($id)
    {
        $farm = $this->farmRepository->getById($id);
        if (!$farm) {
            return response()->json(['message' => 'Farm not found'], 404);
        }
        return new FarmResource($farm->load('address', 'user'));
    }

    public function update(UpdateFarmRequest $request, $id)
    {
        $farm = $this->farmRepository->update($id, $request->validated());
        if (!$farm) {
            return response()->json(['message' => 'Farm not found'], 404);
        }
        return new FarmResource($farm->load('address', 'user'));
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
        );

        return FarmResource::collection($trashedUsers->load('address', 'user'));
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

    public function search(Request $request)
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');
        $perPage = (int) $request->query('perPage', 10);
        $filters = $request->only(['search']);

        $farms = $this->farmRepository->search($sortBy, $sortDirection, $perPage, $filters);
        return FarmResource::collection($farms->load('address', 'user'));
    }
}
