<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupMember;
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
                'name' => $data['name'],
                'owner_id' => $user->id,
                'invite_code' => Str::random(10), 
            ]);

            $group->members()->attach($user->id, [
                'role' => 'Admin',
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
                'role' => 'Member',
                'joined_at' => now(),
            ]);
        }

        return $group;
    }

    public function getGroupSummary(Group $group): array
    {
        return [
            'total_balance' => $group->calculateTotalBalance(),
            'member_count' => $group->members()->count(),
            'recent_transactions' => $group->transactions()
                ->with(['user', 'category'])
                ->latest()
                ->take(10)
                ->get(),
        ];
    }

    public function createSharedExpense(Group $group, User $payer, array $data): Transaction
    {
        return DB::transaction(function () use ($group, $payer, $data) {
            // 1. Create the main transaction record
            $transaction = Transaction::create([
                'user_id'     => $payer->id, // The person who paid
                'group_id'    => $group->id,
                'category_id' => $data['category_id'],
                'amount'      => $data['amount'],
                'date'        => $data['date'] ?? now(),
                'description' => $data['description'],
            ]);

            // 2. Identify all members to share the cost with
            $members = $group->members;
            $memberCount = $members->count();

            if ($memberCount > 0) {
                $shareAmount = $data['amount'] / $memberCount;

                // 3. Create the split records (The "Colocation" logic)
                foreach ($members as $member) {
                    ExpenseSplit::create([
                        'transaction_id' => $transaction->id,
                        'user_id'        => $member->id,
                        'amount_share'   => $shareAmount,
                    ]);
                }
            }

            return $transaction;
        });
    }
}