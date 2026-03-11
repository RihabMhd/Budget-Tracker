@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

    * { box-sizing: border-box; }

    body {
        font-family: 'DM Sans', sans-serif;
        background: #F5F3EE;
        color: #1a1a1a;
    }

    h1, h2, h3, .font-display { font-family: 'Syne', sans-serif; }

    .main-content { margin-left: 255px; padding: 40px 48px; min-height: 100vh; }

    /* ── Flash ── */
    .flash {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #d1fae5;
        border: 1px solid #6ee7b7;
        border-radius: 14px;
        padding: 14px 20px;
        font-size: 13px;
        font-weight: 600;
        color: #065f46;
        margin-bottom: 28px;
        animation: fadeUp 0.35s ease both;
    }

    /* ── Hero card ── */
    .profile-hero {
        background: #1C1C1E;
        border-radius: 24px;
        padding: 36px;
        display: flex;
        align-items: center;
        gap: 28px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        animation: fadeUp 0.4s ease both;
    }

    .profile-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(251,207,151,0.08);
    }

    .profile-hero::after {
        content: '';
        position: absolute;
        bottom: -50px; left: 120px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(46,184,114,0.07);
    }

    .hero-avatar {
        width: 88px; height: 88px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FBCF97, #f7a94b);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif;
        font-size: 32px;
        font-weight: 800;
        color: #1C1C1E;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        box-shadow: 0 0 0 4px rgba(251,207,151,0.25);
    }

    .hero-info { flex: 1; position: relative; z-index: 1; }
    .hero-name { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
    .hero-email { font-size: 13px; color: #666; margin-top: 3px; }
    .hero-since { font-size: 12px; color: #555; margin-top: 8px; }

    .hero-badge-row {
        display: flex;
        gap: 8px;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    .hero-pill {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 99px;
        padding: 5px 12px;
        font-size: 12px;
        color: #aaa;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .hero-pill.green { background: rgba(46,184,114,0.15); border-color: rgba(46,184,114,0.3); color: #2EB872; }
    .hero-pill.peach { background: rgba(251,207,151,0.15); border-color: rgba(251,207,151,0.3); color: #FBCF97; }

    /* ── Stats row ── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .mini-stat {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #ede9e1;
        padding: 20px 22px;
        animation: fadeUp 0.4s ease both;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .mini-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.07); }
    .mini-stat:nth-child(1) { animation-delay: 0.05s; }
    .mini-stat:nth-child(2) { animation-delay: 0.10s; }
    .mini-stat:nth-child(3) { animation-delay: 0.15s; }
    .mini-stat:nth-child(4) { animation-delay: 0.20s; }

    .mini-stat-label { font-size: 11px; color: #bbb; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .mini-stat-value { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
    .mini-stat-sub { font-size: 11px; color: #aaa; margin-top: 3px; }

    /* ── Two-col ── */
    .two-col {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 20px;
        margin-bottom: 24px;
    }

    /* ── Panel ── */
    .panel {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #ede9e1;
        padding: 28px;
        animation: fadeUp 0.4s ease 0.25s both;
    }

    .panel-title {
        font-family: 'Syne', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ── Form ── */
    .form-group { margin-bottom: 18px; }

    .form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 7px;
    }

    .form-input {
        width: 100%;
        padding: 13px 16px;
        border-radius: 14px;
        border: 1.5px solid #ede9e1;
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        color: #1a1a1a;
        background: #fafaf8;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus {
        border-color: #FBCF97;
        box-shadow: 0 0 0 3px rgba(251,207,151,0.2);
        background: #fff;
    }

    .form-input.danger:focus {
        border-color: #f87171;
        box-shadow: 0 0 0 3px rgba(248,113,113,0.15);
    }

    .form-error { font-size: 12px; color: #ef4444; margin-top: 5px; }

    .divider { border: none; border-top: 1px solid #f0ece4; margin: 22px 0; }

    /* ── Buttons ── */
    .btn-save {
        background: #FBCF97;
        border: none;
        border-radius: 14px;
        padding: 13px 24px;
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #1C1C1E;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-save:hover { background: #f7bc71; transform: translateY(-1px); }

    .btn-danger {
        background: #fff0f0;
        border: 1.5px solid #fecaca;
        border-radius: 14px;
        padding: 13px 24px;
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #dc2626;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-danger:hover { background: #fee2e2; }

    /* ── Badges ── */
    .badges-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        gap: 14px;
        margin-bottom: 4px;
    }

    .badge-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        text-align: center;
    }

    .badge-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #fef3c7, #FBCF97);
        border: 2px solid #fde68a;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        transition: transform 0.2s;
    }

    .badge-icon:hover { transform: scale(1.08); }
    .badge-name { font-size: 10px; color: #888; font-weight: 500; line-height: 1.3; }

    .badge-locked .badge-icon {
        background: #f5f3ee;
        border-color: #e8e4dc;
        filter: grayscale(1);
        opacity: 0.4;
    }

    /* ── Transactions ── */
    .tx-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid #f4f1eb;
    }

    .tx-row:last-child { border-bottom: none; }

    .tx-dot {
        width: 36px; height: 36px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .tx-desc { flex: 1; }
    .tx-desc-name { font-size: 13px; font-weight: 600; color: #1a1a1a; }
    .tx-desc-cat { font-size: 11px; color: #bbb; margin-top: 1px; }
    .tx-amt { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; }
    .tx-amt.inc { color: #2EB872; }
    .tx-amt.exp { color: #1a1a1a; }

    /* ── Streak card ── */
    .streak-card {
        background: linear-gradient(135deg, #fff7ec, #fff);
        border: 1px solid #fde8c4;
        border-radius: 18px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
    }

    .streak-flame-big { font-size: 36px; line-height: 1; }
    .streak-label { font-size: 11px; color: #bbb; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
    .streak-num { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; color: #e07a10; line-height: 1; }
    .streak-sub { font-size: 12px; color: #aaa; margin-top: 2px; }

    /* ── Points card ── */
    .points-card {
        background: #2EB872;
        border-radius: 18px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .points-icon { font-size: 30px; }
    .points-label { font-size: 11px; color: rgba(255,255,255,0.6); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
    .points-num { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; color: #fff; line-height: 1; }
    .points-sub { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 2px; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1200px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .two-col   { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .main-content { margin-left: 0; padding: 24px 20px; }
        .profile-hero { flex-direction: column; text-align: center; }
    }
</style>
@endpush

@section('content')
<main class="main-content">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flash">
        <span>✅</span> {{ session('success') }}
    </div>
    @endif

    {{-- ── Hero ── --}}
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

    {{-- ── Mini stats ── --}}
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
            <div class="mini-stat-value">{{ $goalsCompleted }}<span style="font-size:14px;color:#bbb;font-weight:500;"> / {{ $goalsCount }}</span></div>
            <div class="mini-stat-sub">Completed</div>
        </div>
    </div>

    {{-- ── Main two-col ── --}}
    <div class="two-col">

        {{-- Left: Edit profile + Change password --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Edit profile --}}
            <div class="panel">
                <div class="panel-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Edit Profile
                </div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PATCH')

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-input {{ $errors->has('username') ? 'danger' : '' }}"
                               value="{{ old('username', $user->username) }}" required>
                        @error('username') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'danger' : '' }}"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn-save">Save Changes</button>
                </form>
            </div>

            {{-- Change password --}}
            <div class="panel">
                <div class="panel-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Change Password
                </div>

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf @method('PATCH')

                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-input {{ $errors->has('current_password') ? 'danger' : '' }}" required>
                        @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-input {{ $errors->has('password') ? 'danger' : '' }}" required>
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

            {{-- Streak --}}
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

            {{-- Points --}}
            <div class="points-card">
                <div class="points-icon">⭐</div>
                <div>
                    <div class="points-label">Total Points</div>
                    <div class="points-num">{{ number_format($user->points) }}</div>
                    <div class="points-sub">Earned through activity</div>
                </div>
            </div>

            {{-- Badges --}}
            <div class="panel" style="padding:22px;">
                <div class="panel-title" style="margin-bottom:16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
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

            {{-- Recent transactions --}}
            <div class="panel" style="padding:22px;">
                <div class="panel-title" style="margin-bottom:16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
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