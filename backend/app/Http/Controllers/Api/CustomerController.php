<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
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
     */
    public function index()
    {
        $customers = $this->customerRepository->getAll();
        return CustomerResource::collection($customers);
    }

    /**
     * Thêm mới khách hàng.
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
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer = $this->customerRepository->update($id, $request->validated());

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return new CustomerResource($customer);
    }

    /**
     * Xóa khách hàng.
     */
    public function destroy($id)
    {
        $deleted = $this->customerRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
