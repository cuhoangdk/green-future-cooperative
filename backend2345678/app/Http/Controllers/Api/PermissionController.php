<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function getPermissionsByPrefix(Request $request)
    {
        // Lấy tất cả quyền và nhóm theo prefix (users, products, ...)
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0]; // Lấy phần đầu của quyền làm prefix
        });

        // Chuyển đổi từng nhóm permissions thành PermissionResource
        $formattedPermissions = $permissions->map(function ($group) {
            return PermissionResource::collection($group);
        });

        return response()->json([
            'data' => $formattedPermissions
        ]);
    }
}