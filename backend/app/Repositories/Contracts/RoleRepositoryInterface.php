<?php

namespace App\Repositories\Contracts;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function assignPermissions($roleId, array $permissionIds);
}