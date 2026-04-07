<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Services\GoalService;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function index(Request $request, DashboardService $dashboard, GoalService $goals)
    {
        $userId = auth()->id();
        $month = $dashboard->resolveSelectedMonth($request->query('month'));
        $allowance = auth()->user()->monthly_allowance ?? 1000.00;

        return view('analytics.index', [
            'selectedMonth'    => $month,
            'kpis'             => $dashboard->getKpis($userId, $month, $allowance),
            'chartData'        => $dashboard->getBarChartData($userId, $month, $allowance),
            'categorySpending' => $dashboard->getSpendingByCategory($userId, $month, $dashboard->getKpis($userId, $month, $allowance)['monthlyExpenses']),
            'goalData'         => $dashboard->getGoalData($userId),
            'upcomingGoals'    => $goals->getUpcomingGoals($userId),
        ]);
    }
}