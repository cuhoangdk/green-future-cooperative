<?php

namespace App\Repositories\Eloquent;

use App\Models\Parameter;
use App\Repositories\Contracts\ParameterRepositoryInterface;
use Illuminate\Support\Collection;

class ParameterRepository implements ParameterRepositoryInterface
{
    protected $model;

    public function __construct(Parameter $model)
    {
        $this->model = $model;
    }

    public function getByName(string $name): ?Parameter
    {
        return $this->model->where('name', $name)->first();
    }

    public function getByNames(array $names): Collection
    {
        return $this->model->whereIn('name', $names)->get();
    }

    public function updateByName(string $name, array $data): Parameter
    {
        $parameter = $this->getByName($name);
        if ($parameter) {
            $parameter->update($data);
        } else {
            $parameter = $this->model->create(['name' => $name] + $data);
        }
        return $parameter;
    }
}