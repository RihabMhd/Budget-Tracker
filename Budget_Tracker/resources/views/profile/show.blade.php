@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
<main class="main-content">

    @if(session('success'))
    <div class="flash">✅ {{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="flash error">❌ {{ session('error') }}</div>
    @endif

    {{-- Hero --}}
    <div class="profile-hero">
        <div class="hero-avatar">{{ strtoupper(substr($user->username, 0, 2)) }}</div>
        <div class="hero-info">
            <div class="hero-name">{{ $user->username }}</div>
            <div class="hero-email">{{ $user->email }}</div>
            <div class="hero-since">Member since {{ $user->created_at->format('F Y') }}</div>
            <div class="hero-badge-row">
                <div class="hero-pill peach">⭐ {{ number_format($user->points) }} pts</div>
                @if($user->current_streak > 0)
                <div class="hero-pill green">🔥 {{ $user->current_streak }}-day streak</div>
                @endif
                <div class="hero-pill">📊 {{ $txCount }} transactions</div>
                @if($goalsCompleted > 0)
                <div class="hero-pill green">🏆 {{ $goalsCompleted }} {{ Str::plural('goal', $goalsCompleted) }} achieved</div>
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
                {{ $goalsCompleted }}<span style="font-size:14px;color:#bbb;font-weight:500;"> / {{ $goalsCount }}</span>
            </div>
            <div class="mini-stat-sub">Completed</div>
        </div>
    </div>

    {{-- Two-col --}}
    <div class="two-col">

        {{-- Left: Edit profile + Change password --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            <div class="panel">
                <div class="panel-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
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
                        @error('username') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email"
                               class="form-input {{ $errors->has('email') ? 'danger' : '' }}"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn-save">Save Changes</button>
                </form>
            </div>

            <div class="panel">
                <div class="panel-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    Change Password
                </div>
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password"
                               class="form-input {{ $errors->has('current_password') ? 'danger' : '' }}" required>
                        @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password"
                               class="form-input {{ $errors->has('password') ? 'danger' : '' }}" required>
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-input" required>
                    </div>
                    <button type="submit" class="btn-save">Update Password</button>
                </form>
            </div>

        </div>

        {{-- Right: streak, points, badges, recent transactions --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            @if($user->current_streak > 0)
            <div class="streak-card">
                <div class="streak-flame-big">🔥</div>
                <div>
                    <div class="streak-label">Current Streak</div>
                    <div class="streak-num">{{ $user->current_streak }} days</div>
                    <div class="streak-sub">Keep logging to keep it going!</div>
                </div>
            </div>
            @endif

            <div class="points-card">
                <div class="points-icon">⭐</div>
                <div>
                    <div class="points-label">Total Points</div>
                    <div class="points-num">{{ number_format($user->points) }}</div>
                    <div class="points-sub">Earned through activity</div>
                </div>
            </div>

            <div class="panel" style="padding:22px;">
                <div class="panel-title" style="margin-bottom:16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="6"/>
                        <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
                    </svg>
                    Badges
                    <span style="margin-left:auto;font-size:12px;color:#bbb;font-weight:400;">{{ $badges->count() }} earned</span>
                </div>
                @if($badges->isNotEmpty())
                <div class="badges-grid">
                    @foreach($badges as $badge)
                    <div class="badge-item" title="{{ $badge->title }}">
                        <div class="badge-icon">
                            @if($badge->image_path)
                                <img src="{{ asset($badge->image_path) }}" alt="{{ $badge->title }}" style="width:32px;height:32px;object-fit:contain;">
                            @else
                                🏅
                            @endif
                        </div>
                        <div class="badge-name">{{ $badge->title }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <p style="font-size:13px;color:#bbb;text-align:center;padding:16px 0;">No badges yet — keep going! 💪</p>
                @endif
            </div>

            <div class="panel" style="padding:22px;">
                <div class="panel-title" style="margin-bottom:16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    Recent Activity
                </div>
                @forelse($recentTransactions as $tx)
                <div class="tx-row">
                    <div class="tx-dot" style="background:{{ $tx->type === 'Income' ? '#d1fae5' : '#fef3c7' }};">
                        {{ $tx->type === 'Income' ? '💰' : '💸' }}
                    </div>
                    <div class="tx-desc">
                        <div class="tx-desc-name">{{ $tx->description }}</div>
                        <div class="tx-desc-cat">
                            {{ \Carbon\Carbon::parse($tx->date)->format('M j') }}
                            @if($tx->category) · {{ $tx->category->name }} @endif
                        </div>
                    </div>
                    <div class="tx-amt {{ $tx->type === 'Income' ? 'inc' : 'exp' }}">
                        {{ $tx->type === 'Income' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                    </div>
                </div>
                @empty
                <p style="font-size:13px;color:#bbb;text-align:center;padding:12px 0;">No transactions yet.</p>
                @endforelse
            </div>

        </div>
    </div>

</main>
@endsection