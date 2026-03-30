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
        'monthly_budget' => 'float',      
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

    public function ownedGroups()
    {
        return $this->hasMany(Group::class, 'owner_id');
    }
}