<?php

namespace App\Repositories\Eloquent;

use App\Models\Parameter;
use App\Repositories\Contracts\ParameterRepositoryInterface;

class ParameterRepository implements ParameterRepositoryInterface
{
    protected $model;

    public function __construct(Parameter $model)
    {
        $this->model = $model;
    }

    public function getByName(string $name): Parameter // Thêm type hint trả về
    {
        return $this->model->where('name', $name)->firstOrFail();
    }

    public function updateByName(string $name, array $data): Parameter // Thêm type hint trả về
    {
        $parameter = $this->getByName($name);
        $parameter->update($data);
        return $parameter;
    }
}