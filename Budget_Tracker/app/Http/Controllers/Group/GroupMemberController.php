<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMemberController extends Controller
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function destroy(Group $group, User $user)
    {
        $auth = Auth::user();

        if ($auth->id === $user->id) {
            $group->members()->detach($user->id);

            if ($user->id === $group->owner_id) {
                $next = $group->members()->orderByPivot('joined_at')->first();
                if ($next) {
                    $group->update(['owner_id' => $next->id]);
                    $group->members()->updateExistingPivot($next->id, ['role' => 'Admin']);
                } else {
                    $group->delete();
                    return redirect()->route('groups.index')
                        ->with('success', 'You left and the group was deleted.');
                }
            }

            return redirect()->route('groups.index')
                ->with('success', 'You left the group.');
        }

        $this->groupService->kickMember($group, $auth, $user);

        return back()->with('success', "{$user->username} has been removed from the group.");
    }


    public function transfer(Group $group, User $user)
    {
        $this->groupService->transferOwnership($group, Auth::user(), $user);

        return back()->with('success', "Ownership transferred to {$user->username}.");
    }


    public function settle(Group $group, User $user)
    {
        $amount = $this->groupService->settleDebt($group, Auth::user(), $user);

        return back()->with('success',
            number_format($amount, 2) . ' MAD settled with ' . $user->username . '!');
    }
}