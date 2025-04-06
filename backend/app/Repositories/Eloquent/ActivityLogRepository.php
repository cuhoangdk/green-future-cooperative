<?php

namespace App\Repositories\Eloquent;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Carbon\Carbon;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    protected $model;

    public function __construct(ActivityLog $model)
    {
        $this->model = $model;
    }

    public function getAll(string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 10)
    {
        return $this->model->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function getFilteredActivityLog(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
        array $filters = []
    ){
        $query=ActivityLog::query();
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $searchableColumns = ['action', 'entity_type','user_agent'];
            $query->where(function ($query) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $query->orWhere($column, 'like', "%{$search}%");
                }                
            });
        });

        $query->when($filters['user_type'] ?? null, fn($query, $userType)=> $query->where('user_type', $userType));
         // Lọc theo từng mốc ngày riêng lẻ
        $query->when($filters['start_date'] ?? null, fn($query, $startDate) => 
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay())
        );
        $query->when($filters['end_date'] ?? null, fn($query, $endDate) => 
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay())
        );

        $query->orderBy($sortBy, $sortDirection);
    
        return $query->paginate($perPage);
    }
}