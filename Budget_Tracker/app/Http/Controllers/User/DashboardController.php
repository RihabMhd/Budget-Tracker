<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index(Request $request)
    {
        $user             = Auth::user();
        $monthlyAllowance = (float) ($user->monthly_budget ?? 0);
        $selectedMonth    = $this->dashboardService->resolveSelectedMonth($request->get('month'));

        $kpis               = $this->dashboardService->getKpis($user->id, $selectedMonth, $monthlyAllowance);
        $chartData          = $this->dashboardService->getBarChartData($user->id, $selectedMonth, $monthlyAllowance);
        $goalData           = $this->dashboardService->getGoalData($user->id);
        $budgets            = $this->dashboardService->getBudgets($user->id, $selectedMonth);
        $spendingByCategory = $this->dashboardService->getSpendingByCategory($user->id, $selectedMonth, $kpis['monthlyExpenses']);
        $recentTransactions = $this->dashboardService->getRecentTransactions($user->id, $selectedMonth);
        $categories         = $this->dashboardService->getCategories();
        $dailySafeToSpend = $this->dashboardService->getDailyAllowance($kpis['remaining']);
        
        return view('dashboard.index', [
            // allowances and spendings
            'monthlyAllowance'  => $monthlyAllowance,
            'monthlyExpenses'   => $kpis['monthlyExpenses'],
            'remaining'         => $kpis['remaining'],
            'spentPercentage'   => $kpis['spentPercentage'],
            'totalSpentAllTime' => $kpis['totalSpentAllTime'],

            // charts
            'chartMonths'       => $chartData['chartMonths'],
            'chartExpenses'     => $chartData['chartExpenses'],
            'chartAllowances'   => $chartData['chartAllowances'],

            // goals
            'goal'              => $goalData['goal'],
            'goalSaved'         => $goalData['goalSaved'],
            'goalTarget'        => $goalData['goalTarget'],
            'goalPct'           => $goalData['goalPct'],
            'goalTitle'         => $goalData['goalTitle'],
            'goalDeadline'      => $goalData['goalDeadline'],

            // budgets and categories
            'budgets'            => $budgets,
            'spendingByCategory' => $spendingByCategory,
            'categories'         => $categories,
            'recentTransactions' => $recentTransactions,

            // months navigation
            'selectedMonth'     => $selectedMonth,
            'prevMonth'         => $selectedMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth'         => $selectedMonth->copy()->addMonth()->format('Y-m'),
            'isCurrentMonth'    => $selectedMonth->isSameMonth(Carbon::now()),
            'dailySafeToSpend' => $dailySafeToSpend,
        ]);
    }
}