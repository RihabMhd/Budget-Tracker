@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
    <main class="main-content">

        @if (session('success'))
            <div class="flash">✅ {{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="flash error">❌ {{ session('error') }}</div>
        @endif

        {{-- Hero --}}
        <div class="profile-hero">
            <div class="hero-avatar" style="padding:0;overflow:hidden;position:relative;">
                @if ($user->profile_photo)
                    <img src="{{ asset($user->profile_photo) }}" alt="Avatar"
                        style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                @else
                    <span
                        style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">{{ strtoupper(substr($user->username, 0, 2)) }}</span>
                @endif
            </div>
            <div class="hero-info">
                <div class="hero-name">{{ $user->username }}</div>
                <div class="hero-email">{{ $user->email }}</div>
                <div class="hero-since">Member since {{ $user->created_at->format('F Y') }}</div>
                <div class="hero-badge-row">
                    <div class="hero-pill peach">⭐ {{ number_format($user->points) }} pts</div>
                    @if ($user->current_streak > 0)
                        <div class="hero-pill green">🔥 {{ $user->current_streak }}-day streak</div>
                    @endif
                    <div class="hero-pill">📊 {{ $txCount }} transactions</div>
                    @if ($goalsCompleted > 0)
                        <div class="hero-pill green">🏆 {{ $goalsCompleted }} {{ Str::plural('goal', $goalsCompleted) }}
                            achieved</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Mini stats --}}
        <div class="stats-row">
            <div class="mini-stat">
                <div class="mini-stat-label">Net Worth</div>
                <div class="mini-stat-value" style="color:{{ $netWorth >= 0 ? '#2EB872' : '#ef4444' }};">
                    {{ $netWorth >= 0 ? '' : '-' }}${{ number_format(abs($netWorth), 2) }}
                </div>
                <div class="mini-stat-sub">All time</div>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-label">Total Income</div>
                <div class="mini-stat-value">${{ number_format($totalIncome, 2) }}</div>
                <div class="mini-stat-sub">All time</div>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-label">Total Spent</div>
                <div class="mini-stat-value">${{ number_format($totalExpenses, 2) }}</div>
                <div class="mini-stat-sub">All time</div>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-label">Goals</div>
                <div class="mini-stat-value">
                    {{ $goalsCompleted }}<span style="font-size:14px;color:#bbb;font-weight:500;"> /
                        {{ $goalsCount }}</span>
                </div>
                <div class="mini-stat-sub">Completed</div>
            </div>
        </div>

        {{-- Two-col --}}
        <div class="two-col">

            {{-- Left Column --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Profile Picture --}}
                <div class="panel">
                    <div class="panel-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M5.52 19c.63-2.36 2.93-4 6.48-4s5.85 1.64 6.48 4" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        Profile Picture
                    </div>
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
                        <div
                            style="width:64px;height:64px;border-radius:50%;overflow:hidden;flex-shrink:0;background:rgba(251,207,151,0.15);border:2px solid rgba(251,207,151,0.3);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:600;color:#FBCF97;">
                            @if ($user->profile_photo)
                                <img src="{{ asset($user->profile_photo) }}"
                                    style="width:64px;height:64px;object-fit:cover;border-radius:50%;display:block;">
                            @else
                                {{ strtoupper(substr($user->username, 0, 2)) }}
                            @endif
                        </div>
                        <div style="font-size:13px;color:#888;">JPG, PNG or WebP · Max 2MB</div>
                    </div>
                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                        @csrf @method('PATCH')
                        <div class="form-group">
                            <input type="file" name="avatar" class="form-input" accept="image/*" required>
                            @error('avatar')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn-save">Upload Photo</button>
                    </form>
                </div>

                {{-- Monthly Budget --}}
                <div class="panel">
                    <div class="panel-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                        </svg>
                        Monthly Budget
                    </div>
                    @php
                        $budget = $user->monthly_budget ?? 0;
                        $remaining = max(0, $budget - $monthlySpent);
                        $spentPct = $budget > 0 ? min(100, round(($monthlySpent / $budget) * 100)) : 0;
                        $barColor = $spentPct >= 90 ? '#ef4444' : ($spentPct >= 70 ? '#f59e0b' : '#2EB872');
                    @endphp
                    @if ($budget > 0)
                        <div style="margin-bottom:16px;">
                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;">
                                <span style="color:#888;">Spent this month</span>
                                <span style="color:#ccc;font-weight:600;">{{ number_format($monthlySpent, 2) }} /
                                    {{ number_format($budget, 2) }} DH</span>
                            </div>
                            <div
                                style="height:8px;background:#e5d9c8;border-radius:99px;overflow:hidden;margin-bottom:6px;">
                                <div
                                    style="height:100%;width:{{ $spentPct }}%;background:{{ $barColor }};border-radius:99px;">
                                </div>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:12px;">
                                <span style="color:{{ $barColor }};font-weight:600;">{{ $spentPct }}% used</span>
                                <span style="color:#888;">{{ number_format($remaining, 2) }} DH remaining</span>
                            </div>
                        </div>
                    @else
                        <p style="font-size:13px;color:#888;margin-bottom:14px;">No budget set yet. Add your monthly salary
                            below.</p>
                    @endif
                    <form method="POST" action="{{ route('profile.budget') }}">
                        @csrf @method('PATCH')
                        <div class="form-group">
                            <label class="form-label">Monthly Budget (DH)</label>
                            <input type="number" name="monthly_budget" step="0.01" min="0"
                                class="form-input {{ $errors->has('monthly_budget') ? 'danger' : '' }}"
                                value="{{ old('monthly_budget', $user->monthly_budget) }}" placeholder="e.g. 6000"
                                required>
                            @error('monthly_budget')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn-save">Save Budget</button>
                    </form>
                </div>

                {{-- Edit Profile --}}
                <div class="panel">
                    <div class="panel-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Edit Profile
                    </div>
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf @method('PATCH')
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="username"
                                class="form-input {{ $errors->has('username') ? 'danger' : '' }}"
                                value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email"
                                class="form-input {{ $errors->has('email') ? 'danger' : '' }}"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="panel">
                    <div class="panel-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                        Change Password
                    </div>
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf @method('PATCH')
                        <div class="form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password"
                                class="form-input {{ $errors->has('current_password') ? 'danger' : '' }}" required>
                            @error('current_password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password"
                                class="form-input {{ $errors->has('password') ? 'danger' : '' }}" required>
                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-input" required>
                        </div>
                        <button type="submit" class="btn-save">Update Password</button>
                    </form>
                </div>
            </div>

            {{-- Right Column --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                @if ($user->current_streak > 0)
                    <div class="streak-card">
                        <div class="streak-flame-big">🔥</div>
                        <div>
                            <div class="streak-label">Current Streak</div>
                            <div class="streak-num">{{ $user->current_streak }} days</div>
                            <div class="streak-sub">Keep logging to keep it going!</div>
                        </div>
                    </div>
                @endif

                {{-- Rank Hero Card --}}
                @php
                    $currentRank = $user->badges->sortByDesc('points_required')->first();
                    $allRanks = \App\Models\Badge::orderBy('points_required')->get();
                    $nextRank = $allRanks->firstWhere('points_required', '>', $user->points);
                    $prevThreshold = $currentRank?->points_required ?? 0;
                    $nextThreshold = $nextRank?->points_required ?? $prevThreshold;
                    $progressPct =
                        $nextThreshold > $prevThreshold
                            ? min(
                                100,
                                round((($user->points - $prevThreshold) / ($nextThreshold - $prevThreshold)) * 100),
                            )
                            : 100;
                @endphp

                <div class="panel" style="padding:22px; position:relative; overflow:hidden;">
                    <div
                        style="position:absolute;top:-30px;right:-30px;width:130px;height:130px;border-radius:50%;background:rgba(251,207,151,0.06);pointer-events:none;">
                    </div>
                    <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:4px;">
                        🚀 Rank Progression</div>

                    @if ($currentRank)
                        <div style="display:flex;align-items:center;gap:18px;margin-top:14px;">
                            <div
                                style="width:90px;height:90px;flex-shrink:0;border-radius:50%;background:rgba(251,207,151,0.08);border:1px solid rgba(251,207,151,0.2);display:flex;align-items:center;justify-content:center;">
                                <img src="{{ asset($currentRank->image_path) }}" alt="{{ $currentRank->title }}"
                                    style="width:62px;height:62px;object-fit:contain;">
                            </div>
                            <div style="flex:1;">
                                <div style="font-size:18px;font-weight:600;color:#FBCF97;margin-bottom:2px;">
                                    {{ $currentRank->title }}</div>
                                <div
                                    style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;">
                                    Current Mastery Level</div>
                                @if ($nextRank)
                                    <div
                                        style="display:flex;justify-content:space-between;font-size:11px;color:#888;margin-bottom:5px;">
                                        <span>{{ number_format($user->points) }} pts</span>
                                        <span>{{ $nextRank->title }} — {{ number_format($nextRank->points_required) }}
                                            pts</span>
                                    </div>
                                    <div style="height:8px;background:#e5d9c8;border-radius:99px;overflow:hidden;">
                                        <div
                                            style="height:100%;width:{{ $progressPct }}%;background:#BA7517;border-radius:99px;">
                                        </div>
                                    </div>
                                    <div style="font-size:11px;color:#888;margin-top:5px;">
                                        {{ number_format($nextRank->points_required - $user->points) }} pts to next rank
                                    </div>
                                @else
                                    <div style="font-size:12px;color:#FBCF97;">🏆 Max rank achieved!</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p style="font-size:13px;color:#888;padding:16px 0;">No rank yet — keep saving!</p>
                    @endif
                </div>

                {{-- Points Strip --}}
                <div class="panel" style="padding:14px 20px;display:flex;align-items:center;gap:12px;">
                    <div style="font-size:22px;">⭐</div>
                    <div>
                        <div style="font-size:22px;font-weight:600;color:#FBCF97;">{{ number_format($user->points) }}
                        </div>
                        <div style="font-size:12px;color:#888;">Total points</div>
                    </div>
                    <div style="width:1px;height:28px;background:rgba(255,255,255,0.1);margin:0 4px;"></div>
                    @if ($nextRank)
                        <div style="font-size:12px;color:#888;margin-left:auto;">
                            <span
                                style="color:#BA7517;font-weight:600;">{{ number_format($nextRank->points_required - $user->points) }}
                                pts</span> until {{ $nextRank->title }}
                        </div>
                    @endif
                </div>

                {{-- Badges Collection --}}
                <div class="panel" style="padding:22px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <div
                            style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:500;color:#ccc;text-transform:uppercase;letter-spacing:1px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="6" />
                                <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11" />
                            </svg>
                            Badges Collection
                        </div>
                        <span
                            style="font-size:11px;color:#888;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:99px;padding:2px 10px;">{{ $badges->count() }}
                            earned</span>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(78px,1fr));gap:10px;">
                        @foreach ($allRanks as $rank)
                            @php $earned = $badges->contains('id', $rank->id); @endphp
                            <div title="{{ $rank->title }}{{ !$earned ? ' — ' . number_format($rank->points_required) . ' pts required' : '' }}"
                                style="background:rgba(255,255,255,0.03);border:1px solid {{ $earned ? 'rgba(251,207,151,0.35)' : 'rgba(255,255,255,0.07)' }};border-radius:12px;padding:12px 8px;display:flex;flex-direction:column;align-items:center;gap:7px;{{ !$earned ? 'opacity:0.35;filter:grayscale(1);' : '' }}">
                                <img src="{{ asset($rank->image_path) }}" alt="{{ $rank->title }}"
                                    style="width:40px;height:40px;object-fit:contain;">
                                <div
                                    style="font-size:10px;color:{{ $earned ? '#FBCF97' : '#888' }};text-align:center;line-height:1.3;">
                                    {{ $rank->title }}</div>
                            </div>
                        @endforeach
                    </div>

                    @if ($badges->isEmpty())
                        <p style="font-size:13px;color:#888;text-align:center;padding:16px 0;">No badges yet — keep going!
                            💪</p>
                    @endif
                </div>

                {{-- Recent Activity --}}
                <div class="panel" style="padding:22px;">
                    <div class="panel-title" style="margin-bottom:16px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                        Recent Activity
                    </div>
                    @forelse($recentTransactions as $tx)
                        <div class="tx-row">
                            <div class="tx-dot" style="background: #fef3c7;">💸</div>
                            <div class="tx-desc">
                                <div class="tx-desc-name">{{ $tx->description }}</div>
                                <div class="tx-desc-cat">
                                    {{ \Carbon\Carbon::parse($tx->date)->format('M j') }}
                                    @if ($tx->category)
                                        · {{ $tx->category->name }}
                                    @endif
                                </div>
                            </div>
                            <div class="tx-amt exp">-${{ number_format($tx->amount, 2) }}</div>
                        </div>
                    @empty
                        <p style="font-size:13px;color:#bbb;text-align:center;padding:12px 0;">No transactions yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection
