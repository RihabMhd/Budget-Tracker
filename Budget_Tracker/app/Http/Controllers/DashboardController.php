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

        // ── Stat cards ──────────────────────────────────────────────────────────
        $categories      = Category::orderBy('name')->get();
        $monthlyIncome   = Transaction::forUser($user->id)->thisMonth()->income()->sum('amount');
        $monthlyExpenses = Transaction::forUser($user->id)->thisMonth()->expense()->sum('amount');
        $totalBalance    = Transaction::forUser($user->id)->income()->sum('amount')
                         - Transaction::forUser($user->id)->expense()->sum('amount');
        $savingsRate     = $monthlyIncome > 0
                         ? round((($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100)
                         : 0;

        // ── Bar chart: income vs expenses for last 9 months ──────────────────
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

        // ── Savings goal: first active goal ──────────────────────────────────
        $goal = Goal::where('user_id', $user->id)
                    ->whereColumn('current_amount', '<', 'target_amount')
                    ->orderBy('deadline')
                    ->first();

        $goalSaved  = $goal?->current_amount  ?? 0;
        $goalTarget = $goal?->target_amount   ?? 0;
        $goalPct    = ($goalTarget > 0) ? min(100, round(($goalSaved / $goalTarget) * 100)) : 0;
        $goalTitle  = $goal?->title           ?? null;
        $goalDeadline = $goal?->deadline
                        ? Carbon::parse($goal->deadline)->format('F Y')
                        : null;

        // ── Budget left ───────────────────────────────────────────────────────
        $budgets = Budget::with('category')
                         ->where('user_id', $user->id)
                         ->get()
                         ->map(function ($b) {
                             return [
                                 'name'  => $b->category->name,
                                 'color' => $b->category->color ?? '#FBCF97',
                                 'spent' => $b->current_spending,
                                 'limit' => $b->monthly_limit,
                             ];
                         });

        // ── Spending breakdown by category (this month, expenses only) ────────
        $spendingByCategory = Transaction::forUser($user->id)
            ->thisMonth()
            ->expense()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) use ($monthlyExpenses) {
                return [
                    'name'   => $row->category->name,
                    'color'  => $row->category->color ?? '#FBCF97',
                    'amount' => $row->total,
                    'pct'    => $monthlyExpenses > 0
                                ? round(($row->total / $monthlyExpenses) * 100)
                                : 0,
                ];
            });

        // ── Recent transactions ───────────────────────────────────────────────
        $recentTransactions = Transaction::forUser($user->id)
            ->with('category')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit(7)
            ->get();

        return view('dashboard.index', compact(
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
        ));
    }
}