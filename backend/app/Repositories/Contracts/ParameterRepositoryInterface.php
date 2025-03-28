<?php

namespace App\Repositories\Contracts;

use App\Models\Parameter;

interface ParameterRepositoryInterface
{
    public function getByName(string $name): Parameter; 
    public function updateByName(string $name, array $data): Parameter; 
}