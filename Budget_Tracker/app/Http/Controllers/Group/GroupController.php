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
        // Ensure user is a member of the group
        $this->authorize('view', $group);

        $summary = $this->groupService->getGroupSummary($group);

        return view('groups.show', array_merge(['group' => $group], $summary));
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
