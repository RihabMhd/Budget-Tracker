<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

  public function downloadMonthlyReport(Request $request)
{
    // 1. Get the full User Object (not just the username string)
    $user = Auth::user(); 

    $selectedMonth = $this->dashboardService->resolveSelectedMonth($request->get('month'));
    
    // 2. Access properties from the object
    $allowance = (float) ($user->monthly_budget ?? 0);

    $data = [
        // Pass the whole object or just the name to the view
        'userName'     => $user->name ?? $user->username, 
        'month'        => $selectedMonth->format('F Y'),
        'kpis'         => $this->dashboardService->getKpis($user->id, $selectedMonth, $allowance),
        'categories'   => $this->dashboardService->getSpendingByCategory($user->id, $selectedMonth, 0),
        'transactions' => $this->dashboardService->getRecentTransactions($user->id, $selectedMonth),
    ];

    $pdf = Pdf::loadView('exports.monthly_report', $data);

    return $pdf->download("Budget_Report_{$selectedMonth->format('Y_m')}.pdf");
}
}