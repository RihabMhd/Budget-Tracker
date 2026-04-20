<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Requests\Budget\StoreCategoryBudgetRequest;

class BudgetController extends Controller
{
    public function __construct(protected BudgetService $budgetService) {}

    public function index(): View
    {
        return view('budgets.index', $this->budgetService->getIndexData(Auth::user()));
    }

    public function storeCategory(StoreCategoryBudgetRequest $request): RedirectResponse
    {
        $this->budgetService->saveCategoryBudget(Auth::id(), $request->validated());

        return back()->with('success', 'Budget saved successfully.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $this->budgetService->deleteBudget($budget);

        return back()->with('success', 'Budget removed.');
    }
}
