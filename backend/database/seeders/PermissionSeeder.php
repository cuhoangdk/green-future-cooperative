<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Lấy tất cả các route đã định nghĩa
        $routes = Route::getRoutes()->getRoutes();

        $permissions = [];

        foreach ($routes as $route) {
            // Chỉ lấy các route có name
            if ($route->getName() && in_array('permission', $route->middleware())) {
                $permissions[] = [
                    'name' => $route->getName(), // Lấy route name làm permission name
                    'display_name' => $this->generateDisplayName($route->getName()),
                    'description' => 'Quyền truy cập route ' . $route->getName(),
                    'group_name' => $this->generateGroupName($route->getName()),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Chèn hoặc cập nhật permissions vào database
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }

    // Hàm tạo display_name tự động từ route name
    private function generateDisplayName($routeName)
    {
        $parts = explode('.', $routeName);
        $action = end($parts); // Lấy phần cuối (index, store, show, ...)
        $resource = $parts[0]; // Lấy phần đầu (roles, posts, ...)

        switch ($action) {
            case 'index':
                return "Xem danh sách {$resource}";
            case 'store':
                return "Tạo mới {$resource}";
            case 'show':
                return "Xem chi tiết {$resource}";
            case 'update':
                return "Cập nhật {$resource}";
            case 'destroy':
                return "Xóa {$resource}";
            case 'restore':
                return "Khôi phục {$resource}";
            case 'force-delete':
                return "Xoá vĩnh viễn {$resource}";
            default:
                return ucfirst(str_replace('-', ' ', $action)) . " {$resource}";
        }
    }

    // Hàm tạo group_name tự động từ route name
    private function generateGroupName($routeName)
    {
        return explode('.', $routeName)[0]; // Lấy phần đầu của route name làm group_name
    }
}