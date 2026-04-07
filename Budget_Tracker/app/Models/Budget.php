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
    ];

    protected $casts = [
        'monthly_limit' => 'float',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getCurrentMonthSpendingAttribute(): float
    {
        return (float) Transaction::where('category_id', $this->category_id)
            ->where('user_id', $this->user_id)
            ->thisMonth()
            ->sum('amount');
    }

    public function getPercentUsedAttribute(): int
    {
        if ($this->monthly_limit <= 0) return 0;
        return (int) min(100, round(($this->current_month_spending / $this->monthly_limit) * 100));
    }

    public function getRemainingAttribute(): float
    {
        return $this->monthly_limit - $this->current_month_spending;
    }

    public function checkAlert(): bool
    {
        return $this->percent_used >= 80;
    }
}