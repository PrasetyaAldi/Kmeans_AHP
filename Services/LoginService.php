<?php

namespace Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    /**
     * login check auth
     */
    public function login(array $credential)
    {
        // get user by name
        $user = User::where('name', $credential['username'])->first();

        if (!$user->exists()) {
            throw new Exception('User belum terdaftar', 401);
        }

        if (!Hash::check($credential['password'], $user->password)) {
            throw new Exception('Password yang anda masukkan salah', 401);
        }

        return $user;
    }
}
