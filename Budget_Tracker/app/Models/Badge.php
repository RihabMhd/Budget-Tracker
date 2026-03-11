<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'points_required',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'badge_user')
                    ->withTimestamps();
    }
}