<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\ExpenseSplit;

class GroupService
{
    public function createGroup(User $user, array $data): Group
    {
        return DB::transaction(function () use ($user, $data) {
            $group = Group::create([
                'name'        => $data['name'],
                'owner_id'    => $user->id,
                'invite_code' => Str::random(10),
            ]);

            $group->members()->attach($user->id, [
                'role'      => 'Admin',
                'joined_at' => now(),
            ]);

            return $group;
        });
    }

    public function joinGroupByCode(User $user, string $code): Group
    {
        $group = Group::where('invite_code', $code)->firstOrFail();

        if (!$group->members()->where('user_id', $user->id)->exists()) {
            $group->members()->attach($user->id, [
                'role'      => 'Member',
                'joined_at' => now(),
            ]);
        }

        return $group;
    }

    public function createSharedExpense(Group $group, User $payer, array $data): Transaction
    {
        return DB::transaction(function () use ($group, $payer, $data) {
            $otherMembers = $group->members->where('id', '!=', $payer->id);
            $memberCount  = $otherMembers->count();
            $totalMembers = $memberCount + 1; 

            $shareAmount = $memberCount > 0
                ? round($data['amount'] / $totalMembers, 2)
                : $data['amount'];

           
            $transaction = Transaction::create([
                'user_id'     => $payer->id,
                'group_id'    => $group->id,
                'category_id' => $data['category_id'],
                'amount'      => $shareAmount, 
                'date'        => $data['date'] ?? now(),
                'description' => $data['description'],
            ]);

            foreach ($otherMembers as $member) {
                ExpenseSplit::create([
                    'transaction_id' => $transaction->id,
                    'user_id'        => $member->id,
                    'amount_share'   => $shareAmount,
                ]);
            }

            return $transaction;
        });
    }

    public function kickMember(Group $group, User $actor, User $target): void
    {
        $isAdmin = $group->members()
            ->where('user_id', $actor->id)
            ->wherePivot('role', 'Admin')
            ->exists();

        if (!$isAdmin) {
            abort(403, 'Only admins can kick members.');
        }

        if ($target->id === $group->owner_id) {
            abort(403, 'The group owner cannot be kicked.');
        }

        $group->members()->detach($target->id);
    }

    /**
     * Transfer group ownership to another member.
     * Only the current owner can do this.
     */
    public function transferOwnership(Group $group, User $actor, User $newOwner): void
    {
        if ($actor->id !== $group->owner_id) {
            abort(403, 'Only the current owner can transfer ownership.');
        }

        if (!$group->members()->where('user_id', $newOwner->id)->exists()) {
            abort(422, 'The new owner must be a member of the group.');
        }

        DB::transaction(function () use ($group, $actor, $newOwner) {
            // Demote old owner to Member
            $group->members()->updateExistingPivot($actor->id, ['role' => 'Member']);

            // Promote new owner to Admin
            $group->members()->updateExistingPivot($newOwner->id, ['role' => 'Admin']);

            // Update the owner_id on the group
            $group->update(['owner_id' => $newOwner->id]);
        });
    }

    /**
     * Settle a debt: the payer (debtor) pays the creditor.
     *
     * - Clears the debtor's ExpenseSplit records in this group toward the creditor.
     * - Creates a "settlement" Transaction so there's an audit trail:
     *     * debtor's balance decreases  (they spent money paying back)
     *     * creditor's balance increases (they received money)
     * - We record it as a negative-amount transaction on the debtor and a
     *   corresponding income transaction on the creditor, using a dedicated
     *   "Settlement" category (auto-created if missing).
     */
    public function settleDebt(Group $group, User $debtor, User $creditor): float
    {
        return DB::transaction(function () use ($group, $debtor, $creditor) {
            // Find all unpaid splits the debtor owes the creditor in this group
            $splits = ExpenseSplit::where('user_id', $debtor->id)
                ->whereHas(
                    'transaction',
                    fn($q) => $q
                        ->where('group_id', $group->id)
                        ->where('user_id', $creditor->id)
                )->get();

            $totalOwed = $splits->sum('amount_share');

            if ($totalOwed <= 0) {
                abort(422, 'No outstanding debt to settle.');
            }

            // Get or create a "Settlement" category (system-level, no user)
            $category = \App\Models\Category::firstOrCreate(
                ['name' => 'Settlement', 'is_custom' => false],
                ['color' => '#2EB872', 'user_id' => null]
            );

            // Record expense on debtor's side (money leaving their account)
            Transaction::create([
                'user_id'     => $debtor->id,
                'group_id'    => $group->id,
                'category_id' => $category->id,
                'amount'      => $totalOwed,
                'type'        => 'settlement_paid',
                'date'        => now(),
                'description' => "Settlement paid to {$creditor->username}",
            ]);

            // Record income on creditor's side (money coming in)
            Transaction::create([
                'user_id'     => $creditor->id,
                'group_id'    => $group->id,
                'category_id' => $category->id,
                'amount'      => $totalOwed,
                'type'        => 'settlement_received',
                'date'        => now(),
                'description' => "Settlement received from {$debtor->username}",
            ]);

            // Clear the splits so the debt no longer shows up
            $splits->each->delete();

            return $totalOwed;
        });
    }
}
