<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use App\Models\PostCategory;
class PostRepository implements PostRepositoryInterface
{
    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10): Paginator
    {
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('post_status', 'published');
        })->with(['category', 'user'])
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->when(!auth('api_users')->check(), function ($query) {
                $query->where('post_status', 'published');
            })->find($id);
    }


    public function create(array $data)
    {
        // Gắn user_id của người dùng hiện tại vào dữ liệu
        $data['user_id'] = auth('api_users')->id();

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        // Không cho phép thay đổi user_id
        unset($data['user_id']);

        $post = $this->model->find($id);
        if ($post) {
            $post->update($data);
            return $post;
        }
        return null;
    }


    public function delete($id)
    {
        $post = $this->model->find($id);
        if ($post) {
            $post->delete();
            return true;
        }
        return false;
    }
    public function getTrashed(
        string $sortBy = 'deleted_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ) {
        return $this->model->onlyTrashed()
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }
    
    public function getBySlug($slug)
    {
        $query = $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('post_status', 'published');
        });

        $post = $query->where('slug', $slug)->first();

        if ($post && !auth('api_users')->check()) {
            $post->increment('views');
        }

        return $post;
    }


    public function getByCategory(
        $categoryId,
        $perPage = 10,
        $sortDirection = 'desc',
        $sortBy = 'created_at'
    ): Paginator {
        $posts = $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('post_status', 'published');
        })->where('category_id', $categoryId)
            ->orderBy($sortBy, $sortDirection)
            ->with(['category', 'user'])
            ->paginate($perPage);
        return $posts;
    }
    public function getTrashedById($id)
    {
        return $this->model->onlyTrashed()->find($id);
    }

    public function restore($id)
    {
        $post = $this->model->onlyTrashed()->find($id);
        if ($post) {
            return $post->restore();
        }
        return false;
    }

    public function forceDelete($id)
    {
        $post = $this->model->onlyTrashed()->find($id);
        if ($post) {
            return $post->forceDelete();
        }
        return false;
    }
    public function getFilteredPosts(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ) {
        $query = Post::query()
            ->with(['category', 'user'])
            ->when(!auth('api_users')->check(), function ($query) {
                $query->where('post_status', 'published'); // Chỉ hiển thị bài viết đã published cho khách.
            });
    
        // Tìm kiếm
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $searchableColumns = ['title', 'slug', 'summary', 'content'];
            $query->where(function ($query) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $query->orWhere($column, 'like', "%{$search}%");
                }
                $query->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('full_name', 'like', "%{$search}%");
                });
            });
        });
    
        // Lọc theo user
        $query->when($filters['user'] ?? null, fn($query, $user) => $query->where('user_id', $user));
    
        // Lọc theo category
        $query->when($filters['category'] ?? null, fn($query, $category) => $query->where('category_id', $category));
    
        // Lọc theo trạng thái
        $query->when($filters['status'] ?? null, fn($query, $status) => $query->where('post_status', $status));
    
        // Lọc theo khoảng ngày
        $query->when(($filters['start_date'] ?? null) && ($filters['end_date'] ?? null), function ($query) use ($filters) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        });
    
        // Lọc theo từng mốc ngày riêng lẻ
        $query->when($filters['start_date'] ?? null, fn($query, $startDate) => 
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay())
        );
        $query->when($filters['end_date'] ?? null, fn($query, $endDate) => 
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay())
        );
    
        // Lọc bài viết hot - Chỉ áp dụng khi tham số được gửi rõ ràng
        $query->when(
            array_key_exists('is_hot', $filters) && $filters['is_hot'] !== null,
            fn($query) => $query->where('is_hot', $filters['is_hot'])
        );
    
        // Lọc bài viết nổi bật - Chỉ áp dụng khi tham số được gửi rõ ràng
        $query->when(
            array_key_exists('is_featured', $filters) && $filters['is_featured'] !== null,
            fn($query) => $query->where('is_featured', $filters['is_featured'])
        );
    
        // Sắp xếp và phân trang
        $query->orderBy($sortBy, $sortDirection);
    
        return $query->paginate($perPage);
    }

    public function getHotPosts(int $limit = 5)
    {
        return $this->model
            ->when(!auth('api_users')->check(), function ($query) {
                $query->where('post_status', 'published');
            })
            ->where('is_hot', true)
            ->orderBy('hot_order', 'asc')
            ->limit($limit)
            ->get();
    }

    public function getFeaturedPosts(int $limit = 5)
    {
        return $this->model
            ->when(!auth('api_users')->check(), function ($query) {
                $query->where('post_status', 'published');
            })
            ->where('is_featured', true)
            ->orderBy('featured_order', 'asc')
            ->limit($limit)
            ->get();
    }

    public function getCategoryBySlug(string $slug)
    {
        return PostCategory::where('slug', $slug)->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->first();
    }
}
