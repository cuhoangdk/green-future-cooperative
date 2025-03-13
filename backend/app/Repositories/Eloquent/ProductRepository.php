<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with(['category', 'unit', 'user', 'images', 'prices'])->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        });

        // Chỉ áp dụng giới hạn cho user (api_users) không phải super_admin
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);        
    }

    public function getTrashed(
        string $sortBy = 'deleted_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = $this->model->onlyTrashed()->with(['category', 'unit', 'user', 'images', 'prices']);

        // Chỉ giới hạn cho user không phải super_admin
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getById($id)
    {
        $product = $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->with(['category', 'unit', 'user', 'images', 'prices'])->find($id);

        // Chỉ giới hạn cho user không phải super_admin
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin && $product && $product->user_id !== $user->id) {
            return null; // Hoặc throw exception nếu cần
        }

        return $product ?: null; // Trả về null nếu không tìm thấy thay vì throw exception
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Kiểm tra quyền super admin nếu có thay đổi user_id
            $user = Auth::guard('api_users')->user();
            if ($user && !$user->is_super_admin) {
                $data['user_id'] = $user->id;
            }

            return $this->model->create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $product = $this->getById($id);
            if ($product) {
                // Kiểm tra quyền chỉnh sửa product
                $user = Auth::guard('api_users')->user();
                if ($user && !$user->is_super_admin && $product->user_id !== $user->id) {
                    return null; // Hoặc throw exception nếu cần
                }

                if ($user && !$user->is_super_admin) {
                    $data['user_id'] = $user->id;
                }
                $product->update($data);
                return $product;
            }
            return null;
        });
    }

    public function delete($id)
    {
        $product = $this->getById($id);
        if ($product) {
            // Kiểm tra quyền xóa product
            $user = Auth::guard('api_users')->user();
            if ($user && !$user->is_super_admin && $product->user_id !== $user->id) {
                return false;
            }
            $product->delete();
            return $product;
        }
        return false;
    }

    public function getTrashedById($id)
    {
        $product = $this->model->onlyTrashed()->with(['category', 'unit', 'user', 'images', 'prices'])->find($id);

        // Kiểm tra quyền truy cập product đã xóa
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin && $product && $product->user_id !== $user->id) {
            return null;
        }

        return $product ?: null; // Trả về null nếu không tìm thấy
    }

    public function restore($id)
    {
        $product = $this->getTrashedById($id);
        if ($product) {
            // Kiểm tra quyền khôi phục product
            $user = Auth::guard('api_users')->user();
            if ($user && !$user->is_super_admin && $product->user_id !== $user->id) {
                return false;
            }
            $product->restore();
            return $product;
        }
        return false;
    }

    public function forceDelete($id)
    {
        $product = $this->getTrashedById($id);
        if ($product) {
            // Kiểm tra quyền xóa vĩnh viễn product
            $user = Auth::guard('api_users')->user();
            if ($user && !$user->is_super_admin && $product->user_id !== $user->id) {
                return false;
            }
            $product->forceDelete();
            return true;
        }
        return false;
    }

    public function getBySlug($slug)
    {
        $product = $this->model->with(['category', 'unit', 'user', 'images', 'prices'])
            ->when(!auth('api_users')->check(), function ($query) {
                $query->where('is_active', true);
            })
            ->where('slug', $slug)
            ->first();

        // Chỉ giới hạn cho user không phải super_admin
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin && $product && $product->user_id !== $user->id) {
            return null; // Hoặc throw exception nếu cần
        }

        if ($product && !auth('api_users')->check()) {
            $product->increment('views');
        }

        return $product ?: null; // Trả về null nếu không tìm thấy
    }

    public function getByProductCode($productCode)
    {
        $product = $this->model->with(['category', 'unit', 'user', 'images', 'prices'])
            ->when(!auth('api_users')->check(), function ($query) {
                $query->where('is_active', true);
            })
            ->where('product_code', $productCode)
            ->first();

        // Chỉ giới hạn cho user không phải super_admin
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin && $product && $product->user_id !== $user->id) {
            return null; // Hoặc throw exception nếu cần
        }

        return $product ?: null; // Trả về null nếu không tìm thấy
    }

    public function getFilteredProduct(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ): LengthAwarePaginator {
        $query = $this->model->with(['category', 'unit', 'user', 'images', 'prices']);

        // Chỉ áp dụng giới hạn cho user không phải super_admin
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin) {
            $query->where('user_id', $user->id);
        }

        // Áp dụng các bộ lọc nếu có
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($value !== null) {
                    switch ($key) {
                        case 'name':
                        case 'product_code':
                        case 'description':
                            $query->where($key, 'like', "%{$value}%");
                            break;
                        case 'category_id':
                        case 'unit_id':
                        case 'user_id':
                        case 'farm_id':
                            $query->where($key, $value);
                            break;
                        case 'pricing_type':
                            $query->whereIn($key, (array)$value);
                            break;
                        case 'is_active':
                            $query->where($key, filter_var($value, FILTER_VALIDATE_BOOLEAN));
                            break;                        
                        case 'stock_quantity_min':
                            $query->where('stock_quantity', '>=', $value);
                            break;
                        case 'stock_quantity_max':
                            $query->where('stock_quantity', '<=', $value);
                            break;
                        case 'sown_at_from':
                            $query->where('sown_at', '>=', $value);
                            break;
                        case 'sown_at_to':
                            $query->where('sown_at', '<=', $value);
                            break;
                        case 'harvested_at_from':
                            $query->where('harvested_at', '>=', $value);
                            break;
                        case 'harvested_at_to':
                            $query->where('harvested_at', '<=', $value);
                            break;
                    }
                }
            }
        }

        return $query->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function searchByName(
        string $query,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $searchQuery = $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->with(['category', 'unit', 'user', 'images', 'prices']);

        // Chỉ áp dụng giới hạn cho user không phải super_admin
        $user = Auth::guard('api_users')->user();
        if ($user && !$user->is_super_admin) {
            $searchQuery->where('user_id', $user->id);
        }

        return $searchQuery->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('product_code', '=', $query);
        })->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }   
}