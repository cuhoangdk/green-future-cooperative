<?php

namespace App\Repositories\Contracts;
use App\Models\Parameter;
use Illuminate\Support\Collection;

interface ParameterRepositoryInterface
{
    public function getByName(string $name): ?Parameter; 
    public function getByNames(array $names): Collection;
    public function updateByName(string $name, array $data): Parameter; 
}