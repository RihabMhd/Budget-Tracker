<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'invite_code',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function expenseSplits()
    {
        return $this->hasManyThrough(ExpenseSplit::class, Transaction::class);
    }

    public function calculateTotalBalance(): float
    {
        return (float) $this->transactions()->sum('amount');
    }
}