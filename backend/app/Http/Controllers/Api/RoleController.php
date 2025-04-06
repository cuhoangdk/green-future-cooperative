<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Requests\Role\AssignPermissionsRequest;
use App\Http\Resources\RoleResource;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        $roles = $this->roleRepository->getAll();
        return RoleResource::collection($roles);
    }

    public function show($id)
    {
        $role = $this->roleRepository->getById($id);
        $role->load('permissions');
        return new RoleResource($role);
    }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleRepository->create($request->validated());
        return new RoleResource($role);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->roleRepository->update($id, $request->validated());
        return new RoleResource($role);
    }

    public function destroy($id)
    {
        $this->roleRepository->delete($id);
        return response()->json(null, 204);
    }

    public function assignPermissions(AssignPermissionsRequest $request, $id)
    {
        $role = $this->roleRepository->assignPermissions($id, $request->validated()['permissions']);
        $role->load('permissions');
        return new RoleResource($role);
    }
}