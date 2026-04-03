<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $profileService) {}

    public function show()
    {
        $user = Auth::user()->load('badges');

        $txCount        = $user->transactions()->count();
        $totalExpenses  = $user->transactions()->sum('amount');
        $totalIncome    = 0;
        $netWorth       = -$totalExpenses;

        $goalsCount     = $user->goals()->count();
        $goalsCompleted = $user->goals()
            ->whereRaw('current_amount >= target_amount')
            ->count();

        $badges             = $user->badges;
        $recentTransactions = $user->transactions()
            ->with('category')
            ->latest('date')
            ->take(5)
            ->get();

        $monthlySpent = $user->transactions()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('amount');

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
            'monthlySpent',
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

    public function updateBudget(Request $request)
    {
        $request->validate([
            'monthly_budget' => ['required', 'numeric', 'min:0'],
        ]);

        Auth::user()->update([
            'monthly_budget' => $request->monthly_budget,
        ]);

        return back()->with('success', 'Monthly budget updated! 💰');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->profile_photo && file_exists(public_path($user->profile_photo))) {
            unlink(public_path($user->profile_photo));
        }

        $file     = $request->file('avatar');
        $filename = 'avatars/' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('avatars'), basename($filename));

        $user->update(['profile_photo' => $filename]);

        return back()->with('success', 'Profile picture updated! 📸');
    }
}