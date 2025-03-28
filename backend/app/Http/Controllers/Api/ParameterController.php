<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Parameter\UpdateParameterRequest;
use App\Http\Resources\ParameterResource;
use App\Repositories\Eloquent\ParameterRepository;
use App\Http\Controllers\Controller;

class ParameterController extends Controller
{
    protected $repository;

    public function __construct(ParameterRepository $repository)
    {
        $this->repository = $repository;
    }

    public function show()
    {
        $parameter = $this->repository->getByName('shipping_fee');
        return new ParameterResource($parameter);
    }

    public function update(UpdateParameterRequest $request)
    {
        $parameter = $this->repository->updateByName('shipping_fee', $request->only('value'));
        return new ParameterResource($parameter);
    }
}