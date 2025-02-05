<?php
namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthRepository implements AuthRepositoryInterface
{    
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();

        // Lấy client_id & client_secret từ database (hoặc đặt cứng)
        $client = \DB::table('oauth_clients')->where('password_client', true)->first();

        if (!$client) {
            return null;
        }

        // Gửi request lấy token
        $response = Http::asForm()->post(url('/oauth/token'), [
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      => $credentials['email'],
            'password'      => $credentials['password'],
            'scope'         => '*',
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            $user->token()->revoke();
        }
    }

    public function refreshToken()
    {
        // Lấy client_id & client_secret từ database
        $client = \DB::table('oauth_clients')->where('password_client', true)->first();
        if (!$client) {
            return null;
        }

        $response = Http::asForm()->post(url('/oauth/token'), [
            'grant_type'    => 'refresh_token',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'refresh_token' => request('refresh_token'),
            'scope'         => '*',
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }
}
