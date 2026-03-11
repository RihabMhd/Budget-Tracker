<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $categories = Category::where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })->orderBy('name')->get();

        $budgets = Budget::where('user_id', $user->id)
                         ->whereNotNull('category_id')
                         ->get()
                         ->keyBy('category_id');

        $monthlySpending = Transaction::forUser($user->id)
            ->thisMonth()
            ->expense()
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $totalSpentThisMonth = Transaction::forUser($user->id)
                                          ->thisMonth()
                                          ->expense()
                                          ->sum('amount');

        $monthlyLimit = (float) ($user->monthly_budget ?? 0);
        $hasMonthly   = $monthlyLimit > 0;

        return view('budgets.index', compact(
            'categories',
            'budgets',
            'monthlySpending',
            'monthlyLimit',
            'hasMonthly',
            'totalSpentThisMonth',
        ));
    }

    public function storeMonthly(Request $request)
    {
        $request->validate([
            'monthly_limit' => ['required', 'numeric', 'min:1'],
        ]);

        Auth::user()->update(['monthly_budget' => $request->monthly_limit]);

        return back()->with('success', 'Monthly budget updated.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'category_id'   => ['required', 'exists:categories,id'],
            'monthly_limit' => ['required', 'numeric', 'min:1'],
        ]);

        $spending = Transaction::forUser(Auth::id())
            ->thisMonth()
            ->expense()
            ->where('category_id', $request->category_id)
            ->sum('amount');

        Budget::updateOrCreate(
            ['user_id' => Auth::id(), 'category_id' => $request->category_id],
            ['monthly_limit' => $request->monthly_limit, 'current_spending' => $spending]
        );

        return back()->with('success', 'Category budget saved.');
    }

    public function destroy(Budget $budget)
    {
        abort_unless($budget->user_id === Auth::id(), 403);
        $budget->delete();
        return back()->with('success', 'Budget removed.');
    }
}