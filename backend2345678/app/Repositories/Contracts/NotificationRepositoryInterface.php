<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    public function getAllForUser(string $userType, int $userId, int $perPage = 10);
    public function getById(int $id);
    public function create(array $data);
    public function markAsRead(int $id);
    public function markAllAsRead(string $userType, int $userId);
    public function delete(int $id);
}