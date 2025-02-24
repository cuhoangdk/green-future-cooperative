<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    protected $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10)
    {
        return $this->model->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getTrashed(string $sortBy = 'deleted_at', string $sortDirection = 'desc', int $perPage = 10)
    {
        return $this->model->onlyTrashed()->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $role = $this->getById($id);
        $role->update($data);
        return $role;
    }

    public function delete($id)
    {
        $role = $this->getById($id);
        return $role->delete();
    }

    public function getTrashedById($id)
    {
        return $this->model->onlyTrashed()->findOrFail($id);
    }

    public function restore($id)
    {
        $role = $this->getTrashedById($id);
        $role->restore();
        return $role;
    }

    public function forceDelete($id)
    {
        $role = $this->getTrashedById($id);
        return $role->forceDelete();
    }

    public function assignPermissions($roleId, array $permissionIds)
    {
        $role = $this->getById($roleId);
        $role->permissions()->sync($permissionIds);
        return $role;
    }
}