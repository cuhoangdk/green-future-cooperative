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
        unset($data['password']);        
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
        // Tách dữ liệu địa chỉ
        $addressData = [
            'province' => $data['address']['province'] ?? null,
            'district' => $data['address']['district'] ?? null,
            'ward' => $data['address']['ward'] ?? null,
            'street_address' => $data['address']['street_address'] ?? null,
        ];
        unset($data['address']); // Loại bỏ address khỏi dữ liệu chính

        // Nếu is_default = true, cập nhật các địa chỉ khác
        if (isset($data['is_default']) && $data['is_default']) {
            $this->updateIsDefault($customerId);
        }

        // Tạo bản ghi CustomerAddress
        $customerAddress = $this->addressModel->create(array_merge($data, ['customer_id' => $customerId]));

        // Tạo bản ghi Address liên quan
        if ($addressData) {
            $customerAddress->address()->create($addressData);
        }

        return $customerAddress;
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

        // Tách dữ liệu địa chỉ
        $addressData = [
            'province' => $data['address']['province'] ?? null,
            'district' => $data['address']['district'] ?? null,
            'ward' => $data['address']['ward'] ?? null,
            'street_address' => $data['address']['street_address'] ?? null,
        ];
        unset($data['address']); // Loại bỏ address khỏi dữ liệu chính

        // Nếu is_default = true, cập nhật các địa chỉ khác
        if (isset($data['is_default']) && $data['is_default']) {
            $this->updateIsDefault($address->customer_id);
        }

        // Cập nhật CustomerAddress
        $address->update($data);

        // Cập nhật hoặc tạo Address liên quan
        if ($addressData) {
            $address->address()->updateOrCreate(
                ['addressable_id' => $address->id, 'addressable_type' => get_class($address)],
                $addressData
            );
        }

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
