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

   
    // public function transactions()
    // {
    //     return $this->hasMany(Transaction::class);
    // }
    // public function goals()
    // {
    //     return $this->hasMany(Goal::class);
    // }
    // public function badges()
    // {
    //     return $this->hasMany(Badge::class);
    // } 
}
