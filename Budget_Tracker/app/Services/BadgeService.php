<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;

class BadgeService
{
    public function checkAndAward(User $user): array
    {
        $newBadges = Badge::where('points_required', '<=', $user->points)
            ->whereDoesntHave('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        if ($newBadges->isNotEmpty()) {
            $user->badges()->syncWithoutDetaching($newBadges->pluck('id'));
        }

        return $newBadges->all();
    }
}