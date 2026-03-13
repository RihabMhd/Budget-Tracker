<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Goal;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // ── Stats ────────────────────────────────────────────────────────────
        $totalIncome   = Transaction::forUser($user->id)->income()->sum('amount');
        $totalExpenses = Transaction::forUser($user->id)->expense()->sum('amount');
        $netWorth      = $totalIncome - $totalExpenses;
        $txCount       = Transaction::forUser($user->id)->count();
        $goalsCount    = Goal::where('user_id', $user->id)->count();
        $goalsCompleted = Goal::where('user_id', $user->id)
                              ->whereColumn('current_amount', '>=', 'target_amount')
                              ->count();

        // ── Badges ───────────────────────────────────────────────────────────
        $badges = $user->badges()->orderByPivot('created_at', 'desc')->get();

        // ── Recent activity (last 5 transactions) ────────────────────────────
        $recentTransactions = Transaction::forUser($user->id)
            ->with('category')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('profile.show', compact(
            'user',
            'totalIncome',
            'totalExpenses',
            'netWorth',
            'txCount',
            'goalsCount',
            'goalsCompleted',
            'badges',
            'recentTransactions',
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:28', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}