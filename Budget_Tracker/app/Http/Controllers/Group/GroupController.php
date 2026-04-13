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


    public function store(Request $request, Group $group)
    {
        $this->authorize('view', $group);

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

        // Sum of splits where I am the user, but NOT the one who paid the transaction
        $what_i_owe = \App\Models\ExpenseSplit::where('user_id', $userId)
            ->whereHas('transaction', function ($query) use ($userId, $group) {
                $query->where('group_id', $group->id)->where('user_id', '!=', $userId);
            })->sum('amount_share');

        // Sum of splits where I am the payer, but the split belongs to OTHERS
        $what_people_owe_me = \App\Models\ExpenseSplit::where('user_id', '!=', $userId)
            ->whereHas('transaction', function ($query) use ($userId, $group) {
                $query->where('group_id', $group->id)->where('user_id', $userId);
            })->sum('amount_share');

        // Add these to match your view requirements
        $recent_transactions = $group->transactions()->with('user')->latest()->take(10)->get();
        $categories = \App\Models\Category::all();

        // Remove the "..." and list everything explicitly
        return view('groups.show', compact(
            'group',
            'what_i_owe',
            'what_people_owe_me',
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
