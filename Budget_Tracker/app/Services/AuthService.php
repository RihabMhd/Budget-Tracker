<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'username'         => $data['username'],
            'email'            => $data['email'],
            'password'         => Hash::make($data['password']),
            'points'           => 0,
            'current_streak'   => 0,
            'last_activity'    => now(),
        ]);

        Auth::login($user);

        return $user;
    }

    public function login(array $credentials): bool
    {
        if (! Auth::attempt($credentials)) {
            return false;
        }

        Auth::user()->update(['last_activity' => now()]);

        return true;
    }

    public function logout(): void
    {
        Auth::logout();
    }
}