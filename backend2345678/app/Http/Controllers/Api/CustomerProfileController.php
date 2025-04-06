<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateProfileRequest;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Http\Requests\Customer\UpdateAddressRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerAddressResource;
use App\Repositories\Contracts\CustomerProfileRepositoryInterface;
use App\Services\UploadFileService;

class CustomerProfileController extends Controller
{
    protected $profileRepository;
    protected $uploadService;

    public function __construct(CustomerProfileRepositoryInterface $profileRepository, UploadFileService $uploadService)
    {
        $this->profileRepository = $profileRepository;
        $this->uploadService = $uploadService;
    }

    /**
     * Lấy thông tin profile.
     * 
     * @return CustomerResource
     */
    public function show()
    {
        $customer = auth('api_customers')->user();
        $profile = $this->profileRepository->getProfile($customer->id);

        return new CustomerResource($profile);
    }

    /**
     * Cập nhật profile.
     * 
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        $validated = $request->validated();
        $customer = auth('api_customers')->user();
        
        if ($request->hasFile('avatar_url')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            $this->uploadService->deleteImage($customer->avatar_url);

            // Upload ảnh mới
            $validated['avatar_url'] = $this->uploadService->uploadImage($request->file('avatar_url'), 'customers');
        }
        $profile = $this->profileRepository->updateProfile($customer->id, $validated);
        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => new CustomerResource($profile),
        ]);
    }

    /**
     * Xóa tài khoản.
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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function listAddresses()
    {
        $customer = auth('api_customers')->user();
        $addresses = $this->profileRepository->getAddresses($customer->id);

        return CustomerAddressResource::collection($addresses->load('address'));
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

        return response()->json([
            'message' => 'Address added successfully',
            'data' => new CustomerAddressResource($address->load('address')),
        ]);
    }

    /**
     * Cập nhật địa chỉ.
     * 
     * @param UpdateAddressRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAddress(UpdateAddressRequest $request, $id)
    {
        $address = $this->profileRepository->updateAddress($id, $request->validated());

        return response()->json([
            'message' => 'Address updated successfully',
            'data' => new CustomerAddressResource($address->load('address')),
        ]);
    }

    /**
     * Xóa địa chỉ.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAddress($id)
    {
        $this->profileRepository->deleteAddress($id);

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
