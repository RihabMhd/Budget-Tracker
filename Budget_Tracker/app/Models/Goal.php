<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'target_amount',
        'current_amount',
        'deadline',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, (int) round(($this->current_amount / $this->target_amount) * 100));
    }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }
}