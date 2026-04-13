<?php

namespace App\Http\Controllers\Group;

use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function index()
    {
        $groups = Auth::user()->groups()->withCount('members')->get();
        return view('groups.index', compact('groups'));
    }

    /**
     * FIX: Separated into two distinct methods.
     *
     * store() now ONLY handles group creation (was broken — it had a $group
     * parameter and never called createGroup()).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = $this->groupService->createGroup(Auth::user(), $validated);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group created successfully!');
    }

    /**
     * FIX: Added a dedicated method for adding a shared expense to a group.
     * This is what the "Add Shared Expense" form in show.blade.php submits to.
     * Route: POST /groups/{group}/transactions  → groups.transactions.store
     */
    public function storeExpense(Request $request, Group $group)
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'date'        => 'nullable|date',
        ]);

        $this->groupService->createSharedExpense($group, Auth::user(), $validated);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Expense shared and split successfully!');
    }

    public function show(Group $group)
    {
        $userId = auth()->id();

        $group->load('members');

        // Per-person breakdown: how much each member owes ME
        // (I paid the transaction, they have a split record)
        $owes_me = [];
        foreach ($group->members as $member) {
            if ($member->id === $userId) continue;

            $amount = \App\Models\ExpenseSplit::where('user_id', $member->id)
                ->whereHas('transaction', fn($q) => $q
                    ->where('group_id', $group->id)
                    ->where('user_id', $userId)
                )->sum('amount_share');

            if ($amount > 0) {
                $owes_me[] = ['user' => $member, 'amount' => $amount];
            }
        }

        // Per-person breakdown: how much I owe each member
        // (they paid the transaction, I have a split record)
        $i_owe = [];
        foreach ($group->members as $member) {
            if ($member->id === $userId) continue;

            $amount = \App\Models\ExpenseSplit::where('user_id', $userId)
                ->whereHas('transaction', fn($q) => $q
                    ->where('group_id', $group->id)
                    ->where('user_id', $member->id)
                )->sum('amount_share');

            if ($amount > 0) {
                $i_owe[] = ['user' => $member, 'amount' => $amount];
            }
        }

        // Totals for the summary cards
        $what_people_owe_me = collect($owes_me)->sum('amount');
        $what_i_owe         = collect($i_owe)->sum('amount');

        $recent_transactions = $group->transactions()
            ->with(['user', 'expenseSplits'])
            ->latest()
            ->take(10)
            ->get();

        $categories = \App\Models\Category::all();

        return view('groups.show', compact(
            'group',
            'what_i_owe',
            'what_people_owe_me',
            'owes_me',
            'i_owe',
            'recent_transactions',
            'categories'
        ));
    }

    public function join(Request $request)
    {
        $request->validate(['invite_code' => 'required|string']);

        try {
            $group = $this->groupService->joinGroupByCode(Auth::user(), $request->invite_code);
            return redirect()->route('groups.show', $group)
                ->with('success', "Welcome to {$group->name}!");
        } catch (\Exception $e) {
            return back()->withErrors(['invite_code' => 'Invalid invitation code.']);
        }
    }
}