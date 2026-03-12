<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'category_id', 'monthly_limit'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCurrentSpendingAttribute(): float
    {
        return (float) Transaction::where('category_id', $this->category_id)
            ->where('user_id', $this->user_id)
            ->expense()
            ->thisMonth()
            ->sum('amount');
    }

    public function getPercentUsedAttribute(): int
    {
        if ((float) $this->monthly_limit <= 0) return 0;
        return (int) min(100, round(($this->current_spending / (float) $this->monthly_limit) * 100));
    }

    public function getRemainingAttribute(): float
    {
        return (float) $this->monthly_limit - $this->current_spending;
    }
}