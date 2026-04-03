<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;

class BadgeService
{
    /**
     * Check for new badges and award them to the user.
     */
    public function checkAndAward(User $user): array
    {
        // Find badges the user qualifies for but does not yet own
        $newBadges = Badge::where('points_required', '<=', $user->points)
            ->whereDoesntHave('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        if ($newBadges->isNotEmpty()) {
            // SyncWithoutDetaching ensures we only add new ones to the pivot table
            $user->badges()->syncWithoutDetaching($newBadges->pluck('id'));
        }

        return $newBadges->all();
    }
}