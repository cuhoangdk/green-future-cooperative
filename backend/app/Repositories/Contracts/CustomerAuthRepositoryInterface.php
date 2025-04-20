<?php

namespace App\Repositories\Contracts;

interface CustomerAuthRepositoryInterface
{
    public function login(array $credentials);

    public function logout();

    public function register(array $data);

    public function sendResetLink(string $email);

    public function resetPassword(array $credentials);

    public function changePassword(array $data);
    public function verifyAccount(array $data);
    public function refreshToken(string $refreshToken);
    public function resendVerificationToken(string $email);
}
