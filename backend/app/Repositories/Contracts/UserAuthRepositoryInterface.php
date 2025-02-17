<?php

namespace App\Repositories\Contracts;

interface UserAuthRepositoryInterface
{
    public function login(array $credentials);
    public function logout();
    public function refreshToken(string $refreshToken);
}
