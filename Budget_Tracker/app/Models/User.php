<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'profile_photo',
        'points',
        'monthly_budget',   
        'current_streak',
        'last_activity',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_activity'  => 'datetime',
        'points'         => 'integer',
        'current_streak' => 'integer',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
    public function expenseSplits()
    {
        return $this->hasMany(ExpenseSplit::class);
    }
    public function groupMemberships()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'badge_user')->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function checkStreak(): int
    {
        $today    = now()->startOfDay();
        $lastDate = $this->last_activity?->startOfDay();

        if (!$lastDate) {
            $this->current_streak = 1;
        } elseif ($lastDate->eq($today)) {
            // already logged today
        } elseif ($lastDate->eq($today->copy()->subDay())) {
            $this->current_streak++;
        } else {
            $this->current_streak = 1;
        }

        $this->last_activity = now();
        $this->save();

        return $this->current_streak;
    }
}
