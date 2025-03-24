<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForceChangePasswordRequest;
use App\Http\Requests\Customer\SearchCustomerRequest;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Requests\Customer\IndexCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    protected $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Lấy danh sách khách hàng.
     * @param \App\Http\Requests\Customer\IndexCustomerRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexCustomerRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $customers = $this->customerRepository->getAll($sortBy, $sortDirection, $perPage)
        ->appends(request()->query());
        return CustomerResource::collection($customers);
    }

    /**
     * Thêm mới khách hàng.
     * @param \App\Http\Requests\Customer\StoreCustomerRequest $request
     * @return CustomerResource
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        $data['verified_at'] = now();
        $customer = $this->customerRepository->create($data);
        return new CustomerResource($customer);
    }

    /**
     * Hiển thị chi tiết khách hàng.
     * @param mixed $id
     * @return CustomerResource|JsonResponse|mixed
     */
    public function show($id)
    {
        $customer = $this->customerRepository->getById($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return new CustomerResource($customer);
    }

    /**
     * Cập nhật thông tin khách hàng.
     * @param \App\Http\Requests\Customer\UpdateCustomerRequest $request
     * @param mixed $id
     * @return CustomerResource|JsonResponse|mixed
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $data = $request->validated();
        $data['verified_at'] = now();
        $customer = $this->customerRepository->update($id, $data);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return new CustomerResource($customer);
    }

    /**
     * Xóa khách hàng.
     * @param mixed $id
     * @return JsonResponse|mixed
     */
    public function destroy($id)
    {
        $deleted = $this->customerRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json(['message' => 'Customer deleted successfully']);
    }
    /**
     * Lấy danh sách khách hàng đã xóa mềm.
     * 
     * @param IndexCustomerRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function trashed(IndexCustomerRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'deleted_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $trashedUsers = $this->customerRepository->getTrashed(
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            perPage: $perPage
        )->appends(request()->query());

        return CustomerResource::collection($trashedUsers);
    }

    /**
     * Khôi phục khách hàng đã bị xóa.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $restored = $this->customerRepository->restore($id);

        if (!$restored) {
            return response()->json(['message' => 'Customer not found or not trashed'], 404);
        }

        return response()->json(['message' => 'Customer restored successfully']);
    }

    /**
     * Xóa vĩnh viễn khách hàng.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        $deleted = $this->customerRepository->forceDelete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Customer not found or not trashed'], 404);
        }

        return response()->json(['message' => 'Customer permanently deleted successfully']);
    }
    /**
     * Đổi mật khẩu khách hàng.
     * 
     * @param ForceChangePasswordRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function changePassword(ForceChangePasswordRequest $request, $id): JsonResponse
    {
        $result = $this->customerRepository->changePassword($id, $request->validated());

        if ($result) {
            return response()->json(['message' => 'Password changed successfully.'], 200);
        }

        return response()->json(['message' => 'Failed to change password.'], 400);
    }
    /**
     * Tìm kiếm khách hàng theo email, phone_number hoặc full_name.
     * @param SearchCustomerRequest $request
     * @return JsonResponse|mixed|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(SearchCustomerRequest $request)
    {
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $perPage = (int) $request->query('per_page', 10);
        $filters = $request->only(['search', 'province', 'district', 'ward']);

        $customers = $this->customerRepository->search($sortBy, $sortDirection, $perPage, $filters)->appends(request()->query());
        return CustomerResource::collection($customers);
    }

}
