<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Users\SearchUserRequest;
use App\Http\Requests\Users\SearchUserWithFiltersRequest;
use App\Http\Resources\UserResource;
use App\Traits\GeneratesUserCode;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Http\Requests\Users\IndexUserRequest;
use App\Services\UploadFileService;
use App\Http\Requests\Users\StoreUpdateUserRequest;
class UserController extends Controller
{
    use GeneratesUserCode;
    protected $userRepository;
    protected $uploadService;
    public function __construct(UserRepositoryInterface $userRepository, UploadFileService $uploadService)
    {
        $this->userRepository = $userRepository;
        $this->uploadService = $uploadService;
    }
    /**
     * Display a listing of the resource.
     * 
     * @param IndexUserRequest $request - Chứa `per_page`, `sort_by`, `sort_direction`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách bài viết dạng JSON.
     */
    public function index(IndexUserRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $users = $this->userRepository
        ->getAll($sortBy, $sortDirection, $perPage)
        ->appends(request()->query());
        ;

        return UserResource::collection($users);
    }
    
    /**
     * Hiển thị chi tiết một người dùng.
     *
     * @param string $usercode
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function show($usercode)
    {
        $user = $this->userRepository->getByUsercode($usercode);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new UserResource($user);
    }

    /**
     * Tạo mới một người dùng.
     *
     * @param StoreUpdateUserRequest $request
     * @return UserResource
     */
    public function store(StoreUpdateUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']); // Mã hóa mật khẩu
        if ($request->hasFile('avatar_url')) {
            $validated['avatar_url'] = $this->uploadService->uploadImage($request->file('avatar_url'), 'users');
        }

        $user = $this->userRepository->create($validated);

        return new UserResource($user);
    }

    /**
     * Cập nhật thông tin người dùng.
     *
     * @param StoreUpdateUserRequest $request
     * @param int $id
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(StoreUpdateUserRequest $request, $id)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }
        
        $user = $this->userRepository->update($id, $validated);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($request->hasFile('avatar_url')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            $this->uploadService->deleteImage($user->featured_image);

            // Upload ảnh mới
            $validated['avatar_url'] = $this->uploadService->uploadImage($request->file('avatar_url'), 'users');
        }
        return new UserResource($user);
    }

    /**
     * Khôi phục người dùng đã bị xóa.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $restored = $this->userRepository->restore($id);

        if (!$restored) {
            return response()->json(['message' => 'User not found or not trashed'], 404);
        }

        return response()->json(['message' => 'User restored successfully']);
    }

    /**
     * Xóa vĩnh viễn người dùng.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        $deleted = $this->userRepository->forceDelete($id);

        if (!$deleted) {
            return response()->json(['message' => 'User not found or not trashed'], 404);
        }

        return response()->json(['message' => 'User permanently deleted successfully']);
    }
    /**
    * Tìm kiếm người dùng dựa trên từ khóa.
    *
    * @param SearchUserRequest $request
    * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    */
    public function search(SearchUserRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $searchKeyword = $request->input('search'); // Từ khóa tìm kiếm

        $users = $this->userRepository->getSearchUsers($sortBy, $sortDirection, $perPage, $searchKeyword);

        return UserResource::collection($users);
    }
    /**
     * Tìm kiếm người dùng với các bộ lọc.
     *
     * @param SearchUserWithFiltersRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchWithFilters(SearchUserWithFiltersRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $filters = [
            'search' => $request->input('search'), // Từ khóa tìm kiếm
            'is_active' => $request->boolean('is_active'),
            'is_super_admin' => $request->boolean('is_super_admin'),
            'is_banned' => $request->boolean('is_banned'),
            'province' => $request->input('province'),
            'district' => $request->input('district'),
            'ward' => $request->input('ward'),
        ];

        $users = $this->userRepository->getFilteredUsers($sortBy, $sortDirection, $perPage, $filters);

        return UserResource::collection($users);
    }

}
