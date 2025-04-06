<?php

namespace App\Repositories\Contracts;

interface CustomerProfileRepositoryInterface
{
    public function getProfile($customerId);

    public function updateProfile($customerId, array $data);

    public function deleteAccount($customerId);

    public function getAddresses($customerId);

    public function storeAddress($customerId, array $data);

    public function updateAddress($addressId, array $data);

    public function deleteAddress($addressId);
}
