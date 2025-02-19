<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateProfileRequest;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Http\Requests\Customer\UpdateAddressRequest;
use App\Repositories\Contracts\CustomerProfileRepositoryInterface;

class CustomerProfileController extends Controller
{
    protected $profileRepository;

    public function __construct(CustomerProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Lấy thông tin profile.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $customer = auth('api_customers')->user();
        $profile = $this->profileRepository->getProfile($customer->id);

        return response()->json($profile);
    }

    /**
     * Cập nhật profile.
     * 
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        $customer = auth('api_customers')->user();
        $profile = $this->profileRepository->updateProfile($customer->id, $request->validated());

        return response()->json(['message' => 'Profile updated successfully', 'data' => $profile]);
    }

    /**
     * Xóa tài khoản.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $customer = auth('api_customers')->user();
        $this->profileRepository->deleteAccount($customer->id);

        return response()->json(['message' => 'Account deleted successfully']);
    }

    /**
     * Lấy danh sách địa chỉ.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAddresses()
    {
        $customer = auth('api_customers')->user();
        $addresses = $this->profileRepository->getAddresses($customer->id);

        return response()->json($addresses);
    }

    /**
     * Thêm địa chỉ mới.
     * 
     * @param StoreAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeAddress(StoreAddressRequest $request)
    {
        $customer = auth('api_customers')->user();
        $address = $this->profileRepository->storeAddress($customer->id, $request->validated());

        return response()->json(['message' => 'Address added successfully', 'data' => $address]);
    }

    /**
     * Cập nhật địa chỉ.
     * 
     * @param UpdateAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAddress(UpdateAddressRequest $request, $id)
    {
        $address = $this->profileRepository->updateAddress($id, $request->validated());

        return response()->json(['message' => 'Address updated successfully', 'data' => $address]);
    }

    /**
     * Xóa địa chỉ.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAddress($id)
    {
        $this->profileRepository->deleteAddress($id);

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
