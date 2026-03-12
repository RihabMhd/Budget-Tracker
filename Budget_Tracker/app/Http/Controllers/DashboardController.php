<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Models\Goal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $startingAllowance = $user->monthly_budget ?? 0;
        $monthlyIncome   = Transaction::forUser($user->id)->thisMonth()->income()->sum('amount');
        $monthlyExpenses = Transaction::forUser($user->id)
            ->thisMonth()
            ->expense()
            ->sum('amount');
        $remainingWallet = $startingAllowance - $monthlyExpenses;
        $spentPercentage = $startingAllowance > 0
            ? ($monthlyExpenses / $startingAllowance) * 100
            : 0;
        $totalBalance    = Transaction::forUser($user->id)->income()->sum('amount')
            - Transaction::forUser($user->id)->expense()->sum('amount');

        $savingsRate     = $monthlyIncome > 0
            ? round((($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100)
            : 0;

        // 2. Bar Chart: Income vs Expenses (Last 9 Months)
        $chartMonths  = [];
        $chartIncomes = [];
        $chartExpenses = [];

        for ($i = 8; $i >= 0; $i--) {
            $month = Carbon::now()->startOfMonth()->subMonths($i);
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

        // 3. Goals Logic
        $goal = Goal::where('user_id', $user->id)->where('is_active', true)->first();
        $goalSaved    = $goal->current_amount ?? 0;
        $goalTarget   = $goal->target_amount ?? 1;
        $goalPct      = $goalTarget > 0 ? min(100, round(($goalSaved / $goalTarget) * 100)) : 0;
        $goalTitle    = $goal->name ?? 'No active goal';
        $goalDeadline = $goal?->deadline?->format('M j, Y') ?? null;

        // 4. Budgets Logic 
        // We load the 'category' relationship to display names/colors in the UI
        $budgets = Budget::where('user_id', $user->id)
            ->with('category')
            ->whereHas('category')
            ->get();

        // 5. Spending by Category (The Circular Chart / List)
        $spendingByCategory = Transaction::forUser($user->id)
            ->thisMonth()
            ->expense()
            ->whereNotNull('category_id') // Prevent "property of null" errors
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get()
            ->map(function ($row) use ($monthlyExpenses) {
                // Critical Safety: If category was deleted but transaction remains
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
            ->filter() // Removes the null entries
            ->values();

        // 6. Recent Transactions
        $recentTransactions = Transaction::forUser($user->id)
            ->with('category')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit(7)
            ->get();

        // 7. Categories for the "Add Transaction" Modal dropdown
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
            'remainingWallet'
        ));
    }
}