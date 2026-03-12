<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'group_id',
        'amount',
        'date',
        'description',
        'type',
        'receipt_image_path',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'Income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'Expense');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year);
    }

    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->type === 'Income' ? '+' : '-';
        return $prefix . '$' . number_format($this->amount, 2);
    }

    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_image_path
            ? \Illuminate\Support\Facades\Storage::url($this->receipt_image_path)
            : null;
    }
}