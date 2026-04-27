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

        $user = Auth::user();
        $now = now();
        $lastActivity = $user->last_activity;

        if ($lastActivity) {
            $hoursSinceLastActivity = $now->diffInHours($lastActivity);
            $isNewDay = $now->diffInDays($lastActivity) >= 1;

            if ($hoursSinceLastActivity > 48) {
                $user->current_streak = 1;
            } 
            elseif ($isNewDay) {
                $user->current_streak += 1;

                $user->points += $this->calculateStreakPoints($user->current_streak);
            }
        } else {
            $user->current_streak = 1;
        }

        $user->last_activity = $now;
        $user->save();

        return true;
    }

    private function calculateStreakPoints(int $streak): int
    {
        $points = 0;

        if ($streak >= 4) {
            $points += 20;
        }

        if ($streak == 7) $points += 100;
        if ($streak == 14) $points += 250;
        if ($streak == 30) $points += 500;

        return $points;
    }

    public function logout(): void
    {
        Auth::logout();
    }
}