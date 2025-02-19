<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Repositories\Contracts\CustomerProfileRepositoryInterface;

class CustomerProfileRepository implements CustomerProfileRepositoryInterface
{
    protected $customerModel;
    protected $addressModel;

    public function __construct(Customer $customerModel, CustomerAddress $addressModel)
    {
        $this->customerModel = $customerModel;
        $this->addressModel = $addressModel;
    }

    /**
     * Lấy thông tin profile của khách hàng.
     *
     * @param int $customerId
     * @return Customer
     */
    public function getProfile($customerId)
    {
        return $this->customerModel->findOrFail($customerId);
    }

    /**
     * Cập nhật thông tin profile của khách hàng.
     *
     * @param int $customerId
     * @param array $data
     * @return Customer
     */
    public function updateProfile($customerId, array $data)
    {
        $customer = $this->customerModel->findOrFail($customerId);
        $customer->update($data);

        return $customer;
    }

    /**
     * Xóa tài khoản khách hàng.
     *
     * @param int $customerId
     * @return bool
     */
    public function deleteAccount($customerId)
    {
        $customer = $this->customerModel->findOrFail($customerId);
        return $customer->delete();
    }

    /**
     * Lấy danh sách địa chỉ của khách hàng.
     *
     * @param int $customerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAddresses($customerId)
    {
        return $this->addressModel->where('customer_id', $customerId)->get();
    }

    /**
     * Thêm địa chỉ mới cho khách hàng.
     *
     * @param int $customerId
     * @param array $data
     * @return CustomerAddress
     */
    public function storeAddress($customerId, array $data)
    {
        if (isset($data['is_default']) && $data['is_default']) {
            $this->updateIsDefault($customerId);
        }

        return $this->addressModel->create(array_merge($data, ['customer_id' => $customerId]));
    }

    /**
     * Cập nhật địa chỉ của khách hàng.
     *
     * @param int $addressId
     * @param array $data
     * @return CustomerAddress
     */
    public function updateAddress($addressId, array $data)
    {
        $address = $this->addressModel->findOrFail($addressId);

        if (isset($data['is_default']) && $data['is_default']) {
            $this->updateIsDefault($address->customer_id);
        }

        $address->update($data);

        return $address;
    }

    /**
     * Xóa địa chỉ của khách hàng.
     *
     * @param int $addressId
     * @return bool
     */
    public function deleteAddress($addressId)
    {
        $address = $this->addressModel->findOrFail($addressId);
        return $address->delete();
    }

    /**
     * Cập nhật trạng thái `is_default` của tất cả địa chỉ khách hàng thành false.
     *
     * @param int $customerId
     * @return void
     */
    protected function updateIsDefault($customerId)
    {
        $this->addressModel->where('customer_id', $customerId)->update(['is_default' => false]);
    }
}
