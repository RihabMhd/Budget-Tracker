<?php

namespace App\Http\Controllers\Group;

use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\StoreGroupRequest;
use App\Http\Requests\Group\StoreGroupExpenseRequest;
use App\Http\Requests\Group\JoinGroupRequest;

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

    public function store(StoreGroupRequest $request)
    {
        $group = $this->groupService->createGroup(Auth::user(), $request->validated());

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group created successfully!');
    }

    public function storeExpense(StoreGroupExpenseRequest $request, Group $group)
    {
        $this->groupService->createSharedExpense($group, Auth::user(), $request->validated());

        return redirect()->route('groups.show', $group)
            ->with('success', 'Expense shared and split successfully!');
    }

    public function show(Group $group)
    {
        $userId = auth()->id();

        $group->load('members');


        $owes_me = [];
        foreach ($group->members as $member) {
            if ($member->id === $userId) continue;

            $amount = \App\Models\ExpenseSplit::where('user_id', $member->id)
                ->whereHas(
                    'transaction',
                    fn($q) => $q
                        ->where('group_id', $group->id)
                        ->where('user_id', $userId)
                )->sum('amount_share');

            if ($amount > 0) {
                $owes_me[] = ['user' => $member, 'amount' => $amount];
            }
        }

        $i_owe = [];
        foreach ($group->members as $member) {
            if ($member->id === $userId) continue;

            $amount = \App\Models\ExpenseSplit::where('user_id', $userId)
                ->whereHas(
                    'transaction',
                    fn($q) => $q
                        ->where('group_id', $group->id)
                        ->where('user_id', $member->id)
                )->sum('amount_share');

            if ($amount > 0) {
                $i_owe[] = ['user' => $member, 'amount' => $amount];
            }
        }
        
        $what_people_owe_me = collect($owes_me)->sum('amount');
        $what_i_owe         = collect($i_owe)->sum('amount');

        $recent_transactions = $group->transactions()
            ->with(['user', 'expenseSplits'])
            ->latest('date')
            ->paginate(5);

        $categories = \App\Models\Category::where('is_custom', false)
            ->orWhere('user_id', Auth::id())
            ->get();


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

    public function join(JoinGroupRequest $request)
    {
        $request->validate(['invite_code' => 'required|string']);

        try {
            $group = $this->groupService->joinGroupByCode(Auth::user(), $request->validated());
            return redirect()->route('groups.show', $group)
                ->with('success', "Welcome to {$group->name}!");
        } catch (\Exception $e) {
            return back()->withErrors(['invite_code' => 'Invalid invitation code.']);
        }
    }
}
