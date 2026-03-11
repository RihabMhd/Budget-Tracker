<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'monthly_limit',
        'current_spending',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Returns true if current spending has reached or exceeded the monthly limit.
     */
    public function checkAlert(): bool
    {
        return $this->current_spending >= $this->monthly_limit;
    }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->monthly_limit - $this->current_spending);
    }

    public function getUsagePercentAttribute(): int
    {
        if ($this->monthly_limit <= 0) return 0;
        return min(100, (int) round(($this->current_spending / $this->monthly_limit) * 100));
    }
}