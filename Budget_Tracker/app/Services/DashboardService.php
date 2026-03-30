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

        // Clamp: don't allow future months
        if ($selectedMonth->gt(Carbon::now()->startOfMonth())) {
            $selectedMonth = Carbon::now()->startOfMonth();
        }

        return $selectedMonth;
    }

    public function getKpis(int $userId, Carbon $selectedMonth, float $startingAllowance): array
    {
        $monthlyIncome = Transaction::forUser($userId)
            ->whereYear('date', $selectedMonth->year)
            ->whereMonth('date', $selectedMonth->month)
            ->income()
            ->sum('amount');

        $monthlyExpenses = Transaction::forUser($userId)
            ->whereYear('date', $selectedMonth->year)
            ->whereMonth('date', $selectedMonth->month)
            ->expense()
            ->sum('amount');

        $totalBalance = Transaction::forUser($userId)->income()->sum('amount')
            - Transaction::forUser($userId)->expense()->sum('amount');

        return [
            'monthlyIncome'    => $monthlyIncome,
            'monthlyExpenses'  => $monthlyExpenses,
            'remainingWallet'  => $startingAllowance - $monthlyExpenses,
            'spentPercentage'  => $startingAllowance > 0
                ? ($monthlyExpenses / $startingAllowance) * 100
                : 0,
            'totalBalance'     => $totalBalance,
            'savingsRate'      => $monthlyIncome > 0
                ? round((($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100)
                : 0,
        ];
    }

    public function getBarChartData(int $userId, Carbon $selectedMonth): array
    {
        $chartMonths   = [];
        $chartIncomes  = [];
        $chartExpenses = [];

        for ($i = 8; $i >= 0; $i--) {
            $month = $selectedMonth->copy()->subMonths($i);

            $chartMonths[]   = $month->format('M');
            $chartIncomes[]  = (float) Transaction::forUser($userId)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->income()
                ->sum('amount');
            $chartExpenses[] = (float) Transaction::forUser($userId)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->expense()
                ->sum('amount');
        }

        return compact('chartMonths', 'chartIncomes', 'chartExpenses');
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
                    ->expense()
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
            ->expense()
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
}