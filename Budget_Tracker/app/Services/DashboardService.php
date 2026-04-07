<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Models\Goal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function resolveSelectedMonth(?string $monthParam): Carbon
    {
        try {
            $selectedMonth = $monthParam
                ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth()
                : Carbon::now()->startOfMonth();
        } catch (\Exception $e) {
            $selectedMonth = Carbon::now()->startOfMonth();
        }

        if ($selectedMonth->gt(Carbon::now()->startOfMonth())) {
            $selectedMonth = Carbon::now()->startOfMonth();
        }

        return $selectedMonth;
    }

    public function getKpis(int $userId, Carbon $selectedMonth, float $monthlyAllowance): array
    {
        // Total spent this month
        $monthlyExpenses = Transaction::forUser($userId)
            ->whereYear('date', $selectedMonth->year)
            ->whereMonth('date', $selectedMonth->month)
            ->sum('amount');

        // Remaining = allowance - what was spent this month
        $remaining = $monthlyAllowance - $monthlyExpenses;

        // % of allowance spent
        $spentPercentage = $monthlyAllowance > 0
            ? ($monthlyExpenses / $monthlyAllowance) * 100
            : 0;

        // All-time remaining (sum of all allowances - all expenses)
        // We approximate as: total allowance across all months user has been active
        // minus total expenses ever recorded
        $totalExpensesAllTime = Transaction::forUser($userId)->sum('amount');

        return [
            'monthlyExpenses' => $monthlyExpenses,
            'remaining'       => $remaining,
            'spentPercentage' => $spentPercentage,
            'totalSpentAllTime' => $totalExpensesAllTime,
        ];
    }

    public function getBarChartData(int $userId, Carbon $selectedMonth, float $monthlyAllowance): array
    {
        $chartMonths   = [];
        $chartExpenses = [];
        $chartAllowances = [];

        for ($i = 8; $i >= 0; $i--) {
            $month = $selectedMonth->copy()->subMonths($i);

            $chartMonths[]     = $month->format('M');
            $chartExpenses[]   = (float) Transaction::forUser($userId)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
            $chartAllowances[] = $monthlyAllowance; // flat line showing the budget ceiling
        }

        return compact('chartMonths', 'chartExpenses', 'chartAllowances');
    }

    public function getGoalData(int $userId): array
    {
        $goal = Goal::where('user_id', $userId)->where('is_active', true)->first();

        $goalSaved  = $goal->current_amount ?? 0;
        $goalTarget = $goal->target_amount  ?? 1;

        return [
            'goal'         => $goal,
            'goalSaved'    => $goalSaved,
            'goalTarget'   => $goalTarget,
            'goalPct'      => $goalTarget > 0 ? min(100, round(($goalSaved / $goalTarget) * 100)) : 0,
            'goalTitle'    => $goal->name ?? 'No active goal',
            'goalDeadline' => $goal?->deadline?->format('M j, Y') ?? null,
        ];
    }

    public function getBudgets(int $userId, Carbon $selectedMonth): \Illuminate\Support\Collection
    {
        return Budget::where('user_id', $userId)
            ->with('category')
            ->whereHas('category')
            ->get()
            ->map(function ($budget) use ($userId, $selectedMonth) {
                $spent = Transaction::forUser($userId)
                    ->whereYear('date', $selectedMonth->year)
                    ->whereMonth('date', $selectedMonth->month)
                    ->where('category_id', $budget->category_id)
                    ->sum('amount');

                return [
                    'category'         => $budget->category,
                    'monthly_limit'    => $budget->monthly_limit,
                    'current_spending' => $spent,
                    'percent_used'     => $budget->monthly_limit > 0
                        ? min(100, round(($spent / $budget->monthly_limit) * 100))
                        : 0,
                ];
            });
    }

    public function getSpendingByCategory(int $userId, Carbon $selectedMonth, float $monthlyExpenses): \Illuminate\Support\Collection
    {
        return Transaction::forUser($userId)
            ->whereYear('date', $selectedMonth->year)
            ->whereMonth('date', $selectedMonth->month)
            ->whereNotNull('category_id')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get()
            ->map(function ($row) use ($monthlyExpenses) {
                if (! $row->category) return null;

                return [
                    'name'   => $row->category->name,
                    'color'  => $row->category->color ?? '#FBCF97',
                    'amount' => $row->total,
                    'pct'    => $monthlyExpenses > 0
                        ? round(($row->total / $monthlyExpenses) * 100)
                        : 0,
                ];
            })
            ->filter()
            ->values();
    }

    public function getRecentTransactions(int $userId, Carbon $selectedMonth): \Illuminate\Database\Eloquent\Collection
    {
        return Transaction::forUser($userId)
            ->with('category')
            ->whereYear('date', $selectedMonth->year)
            ->whereMonth('date', $selectedMonth->month)
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit(7)
            ->get();
    }

    public function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::orderBy('name')->get();
    }
    
    public function getDailyAllowance($remainingBudget)
    {
        $now = now();
        $daysInMonth = $now->daysInMonth;
        $currentDay = $now->day;

        // Calculate remaining days (including today)
        $daysRemaining = ($daysInMonth - $currentDay) + 1;

        return $remainingBudget > 0 ? $remainingBudget / $daysRemaining : 0;
    }
}
