<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupTransactionController extends Controller
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'date'        => 'nullable|date',
        ]);

        $this->groupService->createSharedExpense($group, Auth::user(), $validated);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Expense added and split among members!');
    }
}