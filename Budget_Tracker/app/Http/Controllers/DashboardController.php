<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Resolve the selected month from ?month=YYYY-MM, defaulting to current month
        try {
            $selectedMonth = $request->get('month')
                ? Carbon::createFromFormat('Y-m', $request->get('month'))->startOfMonth()
                : Carbon::now()->startOfMonth();
        } catch (\Exception $e) {
            $selectedMonth = Carbon::now()->startOfMonth();
        }

        // Clamp: don't allow future months
        if ($selectedMonth->gt(Carbon::now()->startOfMonth())) {
            $selectedMonth = Carbon::now()->startOfMonth();
        }

        $prevMonth      = $selectedMonth->copy()->subMonth()->format('Y-m');
        $nextMonth      = $selectedMonth->copy()->addMonth()->format('Y-m');
        $isCurrentMonth = $selectedMonth->isSameMonth(Carbon::now());

        // ── 1. KPI Stats ──
        $startingAllowance = $user->monthly_budget ?? 0;

        $monthlyIncome = Transaction::forUser($user->id)
            ->whereYear('date', $selectedMonth->year)
            ->whereMonth('date', $selectedMonth->month)
            ->income()
            ->sum('amount');

        $monthlyExpenses = Transaction::forUser($user->id)
            ->whereYear('date', $selectedMonth->year)
            ->whereMonth('date', $selectedMonth->month)
            ->expense()
            ->sum('amount');

        $remainingWallet = $startingAllowance - $monthlyExpenses;

        $spentPercentage = $startingAllowance > 0
            ? ($monthlyExpenses / $startingAllowance) * 100
            : 0;

        // Total balance is always all-time, not filtered by month
        $totalBalance = Transaction::forUser($user->id)->income()->sum('amount')
            - Transaction::forUser($user->id)->expense()->sum('amount');

        $savingsRate = $monthlyIncome > 0
            ? round((($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100)
            : 0;

        // ── 2. Bar Chart: 9 months ending on selected month ──
        $chartMonths   = [];
        $chartIncomes  = [];
        $chartExpenses = [];

        for ($i = 8; $i >= 0; $i--) {
            $month = $selectedMonth->copy()->subMonths($i);
            $chartMonths[] = $month->format('M');

            $chartIncomes[] = (float) Transaction::forUser($user->id)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->income()
                ->sum('amount');

            $chartExpenses[] = (float) Transaction::forUser($user->id)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->expense()
                ->sum('amount');
        }

        // ── 3. Goal ──
        $goal         = Goal::where('user_id', $user->id)->where('is_active', true)->first();
        $goalSaved    = $goal->current_amount ?? 0;
        $goalTarget   = $goal->target_amount ?? 1;
        $goalPct      = $goalTarget > 0 ? min(100, round(($goalSaved / $goalTarget) * 100)) : 0;
        $goalTitle    = $goal->name ?? 'No active goal';
        $goalDeadline = $goal?->deadline?->format('M j, Y') ?? null;

        // ── 4. Budgets — returned as plain arrays to bypass any model accessor overrides ──
        $budgets = Budget::where('user_id', $user->id)
            ->with('category')
            ->whereHas('category')
            ->get()
            ->map(function ($budget) use ($user, $selectedMonth) {
                $spent = Transaction::forUser($user->id)
                    ->whereYear('date', $selectedMonth->year)
                    ->whereMonth('date', $selectedMonth->month)
                    ->expense()
                    ->where('category_id', $budget->category_id)
                    ->sum('amount');

                $percentUsed = $budget->monthly_limit > 0
                    ? min(100, round(($spent / $budget->monthly_limit) * 100))
                    : 0;

                return [
                    'category'         => $budget->category,
                    'monthly_limit'    => $budget->monthly_limit,
                    'current_spending' => $spent,
                    'percent_used'     => $percentUsed,
                ];
            });

        // ── 5. Spending by Category for selected month ──
        $spendingByCategory = Transaction::forUser($user->id)
            ->whereYear('date', $selectedMonth->year)
            ->whereMonth('date', $selectedMonth->month)
            ->expense()
            ->whereNotNull('category_id')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get()
            ->map(function ($row) use ($monthlyExpenses) {
                if (!$row->category) return null;

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

        // ── 6. Recent Transactions for selected month ──
        $recentTransactions = Transaction::forUser($user->id)
            ->with('category')
            ->whereYear('date', $selectedMonth->year)
            ->whereMonth('date', $selectedMonth->month)
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit(7)
            ->get();

        // ── 7. Categories for the modal dropdown ──
        $categories = Category::orderBy('name')->get();

        return view('dashboard.index', compact(
            'startingAllowance',
            'spentPercentage',
            'categories',
            'monthlyIncome',
            'monthlyExpenses',
            'totalBalance',
            'savingsRate',
            'chartMonths',
            'chartIncomes',
            'chartExpenses',
            'goal',
            'goalSaved',
            'goalTarget',
            'goalPct',
            'goalTitle',
            'goalDeadline',
            'budgets',
            'spendingByCategory',
            'recentTransactions',
            'remainingWallet',
            'selectedMonth',
            'prevMonth',
            'nextMonth',
            'isCurrentMonth'
        ));
    }
}