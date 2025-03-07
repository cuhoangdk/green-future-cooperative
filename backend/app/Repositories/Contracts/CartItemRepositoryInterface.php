<?php

namespace App\Repositories\Contracts;

interface CartItemRepositoryInterface
{
    public function getAll(int $customerId);
    public function getById(int $customerId, $id);
    public function create(int $customerId, array $data);
    public function update(int $customerId, $id, array $data);
    public function delete(int $customerId, $id);
}