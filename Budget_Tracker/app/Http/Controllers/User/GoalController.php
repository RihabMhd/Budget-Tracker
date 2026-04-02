<?php

namespace App\Http\Controllers\User;

use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Goal\GoalRequest;
use App\Http\Requests\Goal\AddFundsRequest;

class GoalController extends Controller
{
    public function __construct(private GoalService $goalService) {}

    public function index(): View
    {
        $goals = $this->goalService->getUserGoals(Auth::id());
        $goals->each(fn($g) => $g->append(['progress_percent', 'remaining']));

        return view('goals.index', compact('goals'));
    }

    public function create(): View
    {
        return view('goals.create');
    }

    public function store(GoalRequest $request): RedirectResponse
    {
        $this->goalService->createGoal(Auth::id(), $request->validated());
        return redirect()->route('goals.index')->with('success', 'Goal created successfully! 🎯');
    }

    public function update(GoalRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorizeGoal($goal);
        $this->goalService->updateGoal($goal, $request->validated());
        return redirect()->route('goals.index')->with('success', 'Goal updated successfully! ✏️');
    }

    public function addFunds(AddFundsRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorizeGoal($goal);
        $result = $this->goalService->addFunds($goal, $request->validated()['amount']);

        $message = $result['justCompleted']
            ? '🎉 Congratulations! You reached your savings goal! (+50 pts)'
            : 'Funds added successfully! 💰';

        return redirect()->route('goals.index')->with('success', $message);
    }


    public function edit(Goal $goal): View
    {
        $this->authorizeGoal($goal);

        return view('goals.edit', compact('goal'));
    }



    public function destroy(Goal $goal): RedirectResponse
    {
        $this->authorizeGoal($goal);

        $this->goalService->deleteGoal($goal);

        return redirect()->route('goals.index')
            ->with('success', 'Goal deleted.');
    }



    private function authorizeGoal(Goal $goal): void
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to access this goal.');
        }
    }
}
