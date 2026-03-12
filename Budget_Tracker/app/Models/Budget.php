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


    public function getCurrentSpendingAttribute()
    {
        // This dynamically sums transactions for the category and current month
        return Transaction::where('category_id', $this->category_id)
            ->where('user_id', $this->user_id)
            ->expense()   // Uses scope from Transaction model
            ->thisMonth() // Uses scope from Transaction model
            ->sum('amount') ?? 0;
    }

    public function getPercentUsedAttribute(): int
    {
        if ($this->monthly_limit <= 0) return 0;
        return (int) min(100, round(($this->current_spending / $this->monthly_limit) * 100));
    }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->monthly_limit - $this->current_spending);
    }
}
