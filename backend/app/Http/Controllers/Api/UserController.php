<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\ForceChangePasswordRequest;
use App\Http\Requests\Users\SearchUserRequest;
use App\Http\Requests\Users\SearchUserWithFiltersRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Http\Requests\Users\IndexUserRequest;
use App\Services\UploadFileService;

class UserController extends Controller
{

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
     * @param string $id
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = $this->userRepository->getById($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new UserResource($user);
    }
    

    /**
     * Tạo mới một người dùng.
     *
     * @param StoreUserRequest $request
     * @return UserResource
     */
    public function store(StoreUserRequest $request)
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
     * @param UpdateUserRequest $request
     * @param int $id
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $validated = $request->validated();
        $user = $this->userRepository->getById($id);
        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }
        if ($request->hasFile('avatar_url')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            $this->uploadService->deleteImage($user->avatar_url);

            // Upload ảnh mới
            $validated['avatar_url'] = $this->uploadService->uploadImage($request->file('avatar_url'), 'users');
        }
        $user = $this->userRepository->update($id, $validated);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        return new UserResource($user);
    }
    /**
     * Lấy danh sách người dùng đã xóa mềm.
     * 
     * @param IndexUserRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function trashed(IndexUserRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'deleted_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $trashedUsers = $this->userRepository->getTrashed(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage
        )->appends(request()->query());

        return UserResource::collection($trashedUsers);
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
        $user = $this->userRepository->getById($id);
        if (!$user) {
            return response()->json(['message' => 'User not found or not trashed'], 404);
        }
        $this->uploadService->deleteImage($user->avatar_url);
        $deleted = $this->userRepository->forceDelete($id);

        if (!$deleted) {
            return response()->json(['message' => ' Failed to permanently delete user'], 500);
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

        $filters = ['search' => $searchKeyword];

        $users = $this->userRepository->getSearchUsers($sortBy, $sortDirection, $perPage, $filters)->appends(request()->query());

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
            'is_super_admin' => $request->has('is_super_admin')? $request->boolean('is_super_admin'):null,
            'is_banned' => $request->has('is_banned')? $request->boolean('is_banned'):null,
            'province' => $request->input('province'),
            'district' => $request->input('district'),
            'ward' => $request->input('ward'),
        ];

        $users = $this->userRepository->getFilteredUsers($sortBy, $sortDirection, $perPage, $filters)->appends(request()->query());

        return UserResource::collection($users);
    }
    public function changePassword(ForceChangePasswordRequest $request, $id)
    {
        $result = $this->userRepository->changePassword($id, $request->validated());

        if ($result) {
            return response()->json(['message' => 'Password changed successfully.'], 200);
        }

        return response()->json(['message' => 'Failed to change password.'], 400);
    }
}
