<?php

namespace App\Repositories\Contracts;

interface AuthRepositoryInterface
{
    public function login(array $credentials);
    public function logout();
}
