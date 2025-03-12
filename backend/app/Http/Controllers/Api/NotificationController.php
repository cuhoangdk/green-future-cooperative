<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
    protected $repository;

    public function __construct(NotificationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $userType = $this->getUserType();
        $userId = $this->getUserId();

        if (!$userType || !$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notifications = $this->repository->getAllForUser($userType, $userId, $request->get('per_page', 10));
        return NotificationResource::collection($notifications);
    }

    public function show($id)
    {
        $notification = $this->repository->getById($id);
        if (!$notification || !$this->canAccess($notification)) {
            return response()->json(['message' => 'Notification not found or unauthorized'], 404);
        }
        return new NotificationResource($notification);
    }

    public function markAsRead($id)
    {
        $notification = $this->repository->getById($id);
        if (!$notification || !$this->canAccess($notification)) {
            return response()->json(['message' => 'Notification not found or unauthorized'], 404);
        }
        $this->repository->markAsRead($id);
        return new NotificationResource($notification->refresh());
    }

    public function markAllAsRead()
    {
        $userType = $this->getUserType();
        $userId = $this->getUserId();

        if (!$userType || !$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $this->repository->markAllAsRead($userType, $userId);
        return response()->json(['message' => 'All notifications marked as read'], 200);
    }

    public function destroy($id)
    {
        $notification = $this->repository->getById($id);
        if (!$notification || !$this->canAccess($notification)) {
            return response()->json(['message' => 'Notification not found or unauthorized'], 404);
        }
        $this->repository->delete($id);
        return response()->json(['message' => 'Notification deleted'], 200);
    }

    protected function getUserType()
    {
        if (Auth::guard('api_users')->check()) {
            return 'member';
        } elseif (Auth::guard('api_customers')->check()) {
            return 'customer';
        }
        return null;
    }

    protected function getUserId()
    {
        if (Auth::guard('api_users')->check()) {
            return Auth::guard('api_users')->id();
        } elseif (Auth::guard('api_customers')->check()) {
            return Auth::guard('api_customers')->id();
        }
        return null;
    }

    protected function canAccess($notification)
    {
        $userType = $this->getUserType();
        $userId = $this->getUserId();
        return $notification->user_type === $userType && $notification->user_id === $userId;
    }
}
