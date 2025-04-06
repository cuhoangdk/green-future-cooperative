<?php

namespace App\Repositories\Contracts;

interface UserAuthRepositoryInterface
{
    public function login(array $credentials);
    public function logout();
    public function refreshToken(string $refreshToken);
    public function sendResetLink(string $email);
    public function resetPassword(array $credentials);
    public function changePassword(int $userId, array $data);
    public function getProfile(int $userId);
    public function updateProfile(int $userId, array $data);
    public function deleteProfile(int $userId);

}
