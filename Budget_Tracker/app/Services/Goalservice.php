<?php

namespace App\Services;

use App\Models\Goal;
use Illuminate\Support\Collection;

class GoalService
{

    public function getUserGoals(int $userId): Collection
    {
        return Goal::where('user_id', $userId)
            ->orderBy('deadline')
            ->get();
    }

    public function createGoal(int $userId, array $data): Goal
    {
        return Goal::create([
            'user_id'        => $userId,
            'name'          => $data['name'],
            'target_amount'  => $data['target_amount'],
            'current_amount' => $data['current_amount'] ?? 0,
            'deadline'       => $data['deadline'],
        ]);
    }

    public function updateGoal(Goal $goal, array $data): Goal
    {
        $goal->update(array_filter([
            'name'          => $data['name']          ?? null,
            'target_amount'  => $data['target_amount']  ?? null,
            'current_amount' => $data['current_amount'] ?? null,
            'deadline'       => $data['deadline']        ?? null,
        ], fn($v) => $v !== null));

        return $goal->fresh();
    }

    /**
     * @return array{ goal: Goal, justCompleted: bool }
     */
    public function addFunds(Goal $goal, float $amount): array
    {
        $wasComplete = $goal->current_amount >= $goal->target_amount;

        $goal->current_amount = min(
            $goal->target_amount,
            $goal->current_amount + $amount
        );
        $goal->save();

        $justCompleted = !$wasComplete && ($goal->current_amount >= $goal->target_amount);

        if ($justCompleted) {
            $user = $goal->user; 
            $user->increment('points', 50);
        }

        return [
            'goal'          => $goal,
            'justCompleted' => $justCompleted,
        ];
    }


    public function deleteGoal(Goal $goal): void
    {
        $goal->delete();
    }

    public function getUpcomingGoals(int $userId, int $days = 7): Collection
    {
        return Goal::where('user_id', $userId)
            ->whereDate('deadline', '<=', now()->addDays($days))
            ->whereDate('deadline', '>=', now())
            ->whereColumn('current_amount', '<', 'target_amount')
            ->orderBy('deadline')
            ->get();
    }
}
