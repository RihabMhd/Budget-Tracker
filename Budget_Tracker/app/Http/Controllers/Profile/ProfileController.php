<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $profileService) {}

    public function show()
    {
        // Load user with badges to avoid extra queries in the view
        $user = Auth::user()->load('badges');

        $txCount        = $user->transactions()->count();
        $totalExpenses  = $user->transactions()->sum('amount'); 
        $totalIncome    = 0; 
        $netWorth       = -$totalExpenses;

        $goalsCount     = $user->goals()->count();
        $goalsCompleted = $user->goals()
            ->whereRaw('current_amount >= target_amount')
            ->count();

        // Pass the earned badges collection
        $badges             = $user->badges;
        $recentTransactions = $user->transactions()
            ->with('category')
            ->latest('date')
            ->take(5)
            ->get();

        return view('profile.show', compact(
            'user',
            'txCount',
            'totalIncome',
            'totalExpenses',
            'netWorth',
            'goalsCount',
            'goalsCompleted',
            'badges',
            'recentTransactions',
        ));
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->profileService->updateProfile(Auth::user(), $request->validated());
        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->profileService->updatePassword(Auth::user(), $request->validated()['password']);
        return back()->with('success', 'Password changed successfully.');
    }
}