<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Group\StoreGroupExpenseRequest;

class GroupTransactionController extends Controller
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function store(StoreGroupExpenseRequest $request, Group $group)
    {
        $this->groupService->createSharedExpense($group, Auth::user(), $request->validated());

        return redirect()->route('groups.show', $group)
            ->with('success', 'Expense added and split among members!');
    }
}