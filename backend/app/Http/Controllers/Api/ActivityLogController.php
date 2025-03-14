<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityLog\IndexActivityLogRequest;
use App\Http\Resources\ActivityLogResource;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;

class ActivityLogController extends Controller
{
    protected $repository;

    public function __construct(ActivityLogRepositoryInterface $repository)
    {
        $this->repository = $repository;        
    }

    public function index(IndexActivityLogRequest $request)
    {
        $perPage = $request->input('per_page', 10); // Bỏ giá trị mặc định
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $logs = $this->repository->getAll(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage 
        )->appends(request()->query());        
        return ActivityLogResource::collection($logs);
    }

    public function show($id)
    {
        $log = $this->repository->getById($id);
        return new ActivityLogResource($log);
    }

    public function store(array $data)
    {
        $logData = array_merge($data, [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        if (auth('api_users')->check()) {
            $logData['user_type'] = 'member';
            $logData['user_id'] = auth('api_users')->id();
        } elseif (auth('api_customers')->check()) {
            $logData['user_type'] = 'customer';
            $logData['user_id'] = auth('api_customers')->id();
        } else {
            $logData['user_type'] = 'system';
            $logData['user_id'] = null;
        }

        $log = $this->repository->create($logData);
        return new ActivityLogResource($log);
    }
}