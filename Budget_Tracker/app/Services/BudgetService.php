<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Contracts\Auth\Authenticatable;

class BudgetService
{
    public function getIndexData(Authenticatable $user): array
    {
        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $user->id)
            ->orderBy('name')
            ->get();

        $budgets = Budget::where('user_id', $user->id)
            ->get()
            ->keyBy('category_id');

        $monthlySpending = Transaction::forUser($user->id)
            ->thisMonth()
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return [
            'categories'      => $categories,
            'budgets'         => $budgets,
            'monthlySpending' => $monthlySpending,
        ];
    }

    public function saveCategoryBudget(int $userId, array $data): void
    {
        Budget::updateOrCreate(
            ['user_id' => $userId, 'category_id' => $data['category_id']],
            ['monthly_limit' => $data['monthly_limit']]
        );
    }

    public function deleteBudget(Budget $budget): void
    {
        $budget->delete();
    }
}