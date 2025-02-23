<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Http\Requests\Customer\UpdateAddressRequest;
use App\Http\Resources\CustomerAddressResource;
use App\Repositories\Eloquent\CustomerProfileRepository;

class CustomerAddressController extends Controller
{
    protected $addressRepository;

    public function __construct(CustomerProfileRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * Lấy danh sách địa chỉ của khách hàng.
     */
    public function index($customerId)
    {
        $addresses = $this->addressRepository->getAddresses($customerId);
        return CustomerAddressResource::collection($addresses);
    }

    /**
     * Thêm địa chỉ mới cho khách hàng.
     */
    public function store(StoreAddressRequest $request, $customerId)
    {
        $address = $this->addressRepository->storeAddress($customerId, $request->validated());
        return new CustomerAddressResource($address);
    }


    /**
     * Cập nhật địa chỉ của khách hàng.
     */
    public function update(UpdateAddressRequest $request, $customerId, $id)
    {
        $address = $this->addressRepository->updateAddress( $id, $request->validated());

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        return new CustomerAddressResource($address);
    }

    /**
     * Xóa địa chỉ của khách hàng.
     */
    public function destroy($customerId, $id)
    {
        $deleted = $this->addressRepository->deleteAddress( $id);

        if (!$deleted) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
