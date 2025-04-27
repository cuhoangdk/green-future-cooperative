<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Parameter\UpdateParameterRequest;
use App\Http\Resources\ParameterResource;
use App\Repositories\Eloquent\ParameterRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParameterController extends Controller
{
    protected $repository;

    public function __construct(ParameterRepository $repository)
    {
        $this->repository = $repository;
    }

    public function show(Request $request)
    {
        // Lấy query parameter 'name', mặc định là chuỗi rỗng
        $parameterNameInput = $request->query('name', '');

        // Phân tách chuỗi thành mảng nếu có dấu cộng (+)
        $parameterNames = $parameterNameInput ? explode(' ', trim($parameterNameInput)) : [];

        // Lọc bỏ các name không phải chuỗi hoặc rỗng
        $parameterNames = array_filter($parameterNames, fn($name) => is_string($name) && !empty($name));

        if (empty($parameterNames)) {
            return response()->json(['error' => 'Yêu cầu ít nhất một tên parameter'], 400);
        }

        $parameters = $this->repository->getByNames($parameterNames);

        if ($parameters->isEmpty()) {
            return response()->json(['error' => 'Không tìm thấy parameter nào'], 404);
        }

        return ParameterResource::collection($parameters);
    }

    public function update(UpdateParameterRequest $request)
    {
        $parameterName = $request->input('name');
        if (!$parameterName) {
            return response()->json(['error' => 'Parameter name is required'], 400);
        }

        $parameter = $this->repository->updateByName($parameterName, $request->only('value'));
        if (!$parameter) {
            return response()->json(['error' => 'Parameter not found'], 404);
        }

        return new ParameterResource($parameter);
    }
}