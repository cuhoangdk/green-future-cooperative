<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function getAll(?int $customerId = null, int $perPage = 10);
    public function getById(?int $customerId = null, $id);
    public function createForCustomer(int $customerId, array $data);
    public function createForAdmin(int $customerId, array $data);
    public function update(?int $customerId = null, $id, array $data);
    public function cancel(?int $customerId = null, $id, array $data);
}