@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

    * { box-sizing: border-box; }

    body {
        font-family: 'DM Sans', sans-serif;
        background: #F5F3EE;
        color: #1a1a1a;
    }

    h1, h2, h3, .font-display {
        font-family: 'Syne', sans-serif;
    }

    /* ── Main ── */
    .main-content {
        margin-left: 255px;
        padding: 40px 48px;
        min-height: 100vh;
    }

    /* ── Top bar ── */
    .topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 36px;
    }

    .topbar-title { font-size: 28px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
    .topbar-subtitle { font-size: 14px; color: #888; margin-top: 2px; font-weight: 400; }

    .month-selector {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid #e8e4dc;
        border-radius: 14px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        cursor: pointer;
        transition: all 0.2s;
    }

    .month-selector:hover { border-color: #FBCF97; box-shadow: 0 0 0 3px #fbcf9720; }

    /* ── Stat Cards ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #ede9e1;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.07); }

    .stat-card.accent-peach { background: #FBCF97; border-color: #FBCF97; }
    .stat-card.accent-green { background: #2EB872; border-color: #2EB872; }

    .stat-card.accent-peach .stat-label,
    .stat-card.accent-peach .stat-change { color: #8a5a1a; }
    .stat-card.accent-peach .stat-value { color: #1C1C1E; }

    .stat-card.accent-green .stat-label,
    .stat-card.accent-green .stat-change { color: #0a5530; }
    .stat-card.accent-green .stat-value { color: #fff; }

    .stat-icon {
        width: 38px; height: 38px;
        border-radius: 12px;
        background: #F5F3EE;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px;
    }

    .stat-card.accent-peach .stat-icon { background: rgba(255,255,255,0.4); }
    .stat-card.accent-green .stat-icon { background: rgba(255,255,255,0.2); }

    .stat-icon svg {
        width: 18px; height: 18px;
        stroke: #1C1C1E;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .stat-label { font-size: 12px; color: #999; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; color: #1a1a1a; margin: 4px 0; letter-spacing: -1px; }
    .stat-change { font-size: 12px; font-weight: 500; }
    .stat-change.up { color: #2EB872; }
    .stat-change.down { color: #e05c5c; }

    .stat-bg-decoration {
        position: absolute;
        right: -10px; bottom: -10px;
        width: 70px; height: 70px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
    }

    /* ── Two-col grid ── */
    .mid-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 20px;
        margin-bottom: 24px;
    }

    /* ── Panel ── */
    .panel {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #ede9e1;
        padding: 28px;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .panel-title { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; color: #1a1a1a; }
    .panel-action { font-size: 12px; color: #2EB872; font-weight: 600; text-decoration: none; }
    .panel-action:hover { text-decoration: underline; }

    /* ── Bar Chart ── */
    .bar-chart {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        height: 140px;
        padding-bottom: 8px;
    }

    .bar-group { display: flex; flex-direction: column; align-items: center; flex: 1; gap: 6px; }

    .bar-wrap {
        width: 100%;
        display: flex;
        gap: 3px;
        align-items: flex-end;
        height: 120px;
    }

    .bar {
        flex: 1;
        border-radius: 6px 6px 0 0;
        transition: opacity 0.2s;
        cursor: pointer;
    }
    .bar:hover { opacity: 0.8; }
    .bar.income { background: #2EB872; }
    .bar.expense { background: #FBCF97; }

    .bar-label { font-size: 11px; color: #aaa; font-weight: 500; }

    .chart-legend {
        display: flex;
        gap: 16px;
        margin-top: 12px;
    }

    .legend-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 3px;
    }

    .legend-item { display: flex; align-items: flex-start; gap: 6px; font-size: 12px; color: #888; }

    /* ── Budget categories ── */
    .category-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #f4f1eb;
    }

    .category-item:last-child { border-bottom: none; padding-bottom: 0; }

    .category-icon {
        width: 40px; height: 40px;
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .category-meta { flex: 1; min-width: 0; }
    .category-name { font-size: 13px; font-weight: 600; color: #1a1a1a; }

    .category-bar-wrap {
        height: 4px;
        background: #f0ece4;
        border-radius: 99px;
        margin-top: 5px;
        overflow: hidden;
    }

    .category-bar-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 0.6s ease;
    }

    .category-amount { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; color: #1a1a1a; text-align: right; white-space: nowrap; }
    .category-pct { font-size: 11px; color: #aaa; text-align: right; }

    /* ── Transactions ── */
    .transaction-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px 0;
        border-bottom: 1px solid #f4f1eb;
    }

    .transaction-item:last-child { border-bottom: none; }

    .tx-icon {
        width: 42px; height: 42px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .tx-meta { flex: 1; min-width: 0; }
    .tx-name { font-size: 13px; font-weight: 600; color: #1a1a1a; }
    .tx-date { font-size: 11px; color: #bbb; margin-top: 1px; }
    .tx-amount { font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 700; }
    .tx-amount.credit { color: #2EB872; }
    .tx-amount.debit { color: #1a1a1a; }

    /* ── Savings Goal ── */
    .goal-card {
        background: #1C1C1E;
        border-radius: 20px;
        padding: 24px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .goal-card::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(251, 207, 151, 0.12);
    }

    .goal-card::after {
        content: '';
        position: absolute;
        bottom: -40px; left: 20px;
        width: 90px; height: 90px;
        border-radius: 50%;
        background: rgba(46, 184, 114, 0.1);
    }

    .goal-title { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700; margin-bottom: 4px; }
    .goal-subtitle { font-size: 12px; color: #666; margin-bottom: 20px; }

    .goal-amounts { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 12px; }
    .goal-saved { font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 800; color: #FBCF97; }
    .goal-target { font-size: 12px; color: #555; }

    .goal-progress-wrap {
        height: 6px;
        background: #2a2a2c;
        border-radius: 99px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .goal-progress-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, #2EB872, #FBCF97);
        transition: width 0.8s ease;
    }

    .goal-pct { font-size: 12px; color: #555; }
    .goal-pct strong { color: #2EB872; }

    /* ── Streak ── */
    .streak-row {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff7ec;
        border: 1px solid #fde8c4;
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 20px;
    }

    .streak-flame { font-size: 20px; }
    .streak-text { font-size: 13px; font-weight: 600; color: #9a5a10; }
    .streak-text span { color: #e07a10; }

    /* ── Bottom grid ── */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* ── Quick add ── */
    .quick-add-btn {
        width: 100%;
        background: #FBCF97;
        border: none;
        border-radius: 14px;
        padding: 13px;
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #1C1C1E;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .quick-add-btn:hover { background: #f7bc71; transform: translateY(-1px); }
    .quick-add-btn svg { width: 16px; height: 16px; stroke: #1C1C1E; fill: none; stroke-width: 2.5; stroke-linecap: round; }

    /* ── Animations ── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card  { animation: fadeUp 0.4s ease both; }
    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.10s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
    .stat-card:nth-child(4) { animation-delay: 0.20s; }
    .panel      { animation: fadeUp 0.4s ease 0.25s both; }

    /* ── Responsive ── */
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .mid-grid   { grid-template-columns: 1fr; }
        .bottom-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .sidebar { transform: translateX(-260px); }
        .main-content { margin-left: 0; padding: 24px 20px; }
    }
</style>
@endpush

@section('content')

{{-- ══════════ MAIN ══════════ --}}
<main class="main-content">

    {{-- Top bar --}}
    <div class="topbar">
        <div>
            <div class="topbar-title">Good morning, {{ Auth::user()->username }} 👋</div>
            <div class="topbar-subtitle">Here's your financial snapshot for this month.</div>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="month-selector">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:none;stroke:#888;stroke-width:2;stroke-linecap:round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ now()->format('F Y') }}
                <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:#888;stroke-width:2.5;stroke-linecap:round"><polyline points="6,9 12,15 18,9"/></svg>
            </button>
            <button class="quick-add-btn" style="width:auto;padding:13px 20px;" onclick="openModal()">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Transaction
            </button>
        </div>
    </div>

    {{-- Streak --}}
    @if(Auth::user()->current_streak > 0)
    <div class="streak-row">
        <span class="streak-flame">🔥</span>
        <span class="streak-text">You're on a <span>{{ Auth::user()->current_streak }}-day streak</span> — keep logging your expenses!</span>
    </div>
    @endif

    {{-- ── Stat Cards ── --}}
    <div class="stats-grid">
        {{-- Total Balance --}}
        <div class="stat-card accent-peach">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
            </div>
            <div class="stat-label">Total Balance</div>
            <div class="stat-value">${{ number_format($totalBalance ?? 12480.50, 2) }}</div>
            <div class="stat-change up">↑ 8.2% from last month</div>
            <div class="stat-bg-decoration"></div>
        </div>

        {{-- Monthly Income --}}
        <div class="stat-card accent-green">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24"><polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/><polyline points="17,6 23,6 23,12"/></svg>
            </div>
            <div class="stat-label">Income</div>
            <div class="stat-value">${{ number_format($monthlyIncome ?? 5200.00, 2) }}</div>
            <div class="stat-change up" style="color:#a8f0cc;">↑ 3.1% vs last month</div>
            <div class="stat-bg-decoration"></div>
        </div>

        {{-- Monthly Expenses --}}
        <div class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24"><polyline points="23,18 13.5,8.5 8.5,13.5 1,6"/><polyline points="17,18 23,18 23,12"/></svg>
            </div>
            <div class="stat-label">Expenses</div>
            <div class="stat-value">${{ number_format($monthlyExpenses ?? 3140.20, 2) }}</div>
            <div class="stat-change down">↑ 12% vs last month</div>
        </div>

        {{-- Savings Rate --}}
        <div class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            </div>
            <div class="stat-label">Savings Rate</div>
            <div class="stat-value">{{ $savingsRate ?? 39 }}%</div>
            <div class="stat-change up">↑ Great pace this month</div>
        </div>
    </div>

    {{-- ── Mid grid: chart + categories ── --}}
    <div class="mid-grid">

        {{-- Income vs Expenses chart --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Income vs Expenses</div>
                <a href="#" class="panel-action">View report →</a>
            </div>
            <div class="bar-chart" id="barChart">
                @php
                    $maxVal = max(1, max(array_merge($chartIncomes, $chartExpenses)));
                @endphp
                @foreach($chartMonths as $i => $month)
                    @php
                        $incH   = round(($chartIncomes[$i]  / $maxVal) * 110);
                        $expH   = round(($chartExpenses[$i] / $maxVal) * 110);
                        $isLast = $i === count($chartMonths) - 1;
                    @endphp
                    <div class="bar-group">
                        <div class="bar-wrap">
                            <div class="bar income"  style="height:{{ $incH }}px; opacity:{{ $isLast ? 1 : 0.55 }};"></div>
                            <div class="bar expense" style="height:{{ $expH }}px; opacity:{{ $isLast ? 1 : 0.55 }};"></div>
                        </div>
                        <div class="bar-label" style="{{ $isLast ? 'color:#1a1a1a;font-weight:700;' : '' }}">{{ $month }}</div>
                    </div>
                @endforeach
            </div>
            <div class="chart-legend">
                <div class="legend-item"><div class="legend-dot" style="background:#2EB872;margin-top:4px;"></div> Income</div>
                <div class="legend-item"><div class="legend-dot" style="background:#FBCF97;margin-top:4px;"></div> Expenses</div>
            </div>
        </div>

        {{-- Savings goal --}}
        <div style="display:flex;flex-direction:column;gap:18px;">
            <div class="goal-card">
                @if($goal)
                    <div class="goal-title">🎯 {{ $goalTitle }}</div>
                    <div class="goal-subtitle">{{ $goalDeadline ? 'Target: ' . $goalDeadline : 'No deadline set' }}</div>
                    <div class="goal-amounts">
                        <div>
                            <div style="font-size:11px;color:#555;margin-bottom:2px;">SAVED SO FAR</div>
                            <div class="goal-saved">${{ number_format($goalSaved) }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:11px;color:#555;margin-bottom:2px;">TARGET</div>
                            <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#888;">${{ number_format($goalTarget) }}</div>
                        </div>
                    </div>
                    <div class="goal-progress-wrap">
                        <div class="goal-progress-fill" style="width:{{ $goalPct }}%;"></div>
                    </div>
                    <div class="goal-pct"><strong>{{ $goalPct }}% complete</strong> — ${{ number_format($goalTarget - $goalSaved) }} to go</div>
                @else
                    <div class="goal-title">🎯 No active goal</div>
                    <div class="goal-subtitle" style="margin-bottom:20px;">Set a savings goal to track your progress here.</div>
                    @if(\Illuminate\Support\Facades\Route::has('goals.create'))
                        <a href="{{ route('goals.create') }}" style="display:inline-block;background:#FBCF97;color:#1C1C1E;font-family:'Syne',sans-serif;font-weight:700;font-size:13px;padding:10px 18px;border-radius:12px;text-decoration:none;">+ Create a Goal</a>
                    @endif
                @endif
            </div>

            {{-- Budget remaining --}}
            <div class="panel" style="padding:20px;">
                <div class="panel-header" style="margin-bottom:14px;">
                    <div class="panel-title" style="font-size:14px;">Budget Left</div>
                    <a href="#" class="panel-action">Manage</a>
                </div>
                @forelse($budgets as $cat)
                    @php $pct = $cat['limit'] > 0 ? min(100, round(($cat['spent'] / $cat['limit']) * 100)) : 0; @endphp
                    <div style="margin-bottom:12px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                            <span style="font-size:13px;font-weight:600;color:#1a1a1a;">{{ $cat['name'] }}</span>
                            <span style="font-size:12px;color:#aaa;">${{ number_format($cat['spent'], 2) }} / ${{ number_format($cat['limit'], 2) }}</span>
                        </div>
                        <div style="height:5px;background:#f0ece4;border-radius:99px;overflow:hidden;">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 90 ? '#ef4444' : $cat['color'] }};border-radius:99px;"></div>
                        </div>
                    </div>
                @empty
                    <p style="font-size:13px;color:#aaa;text-align:center;padding:12px 0;">No budgets set up yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Bottom: Spending categories + Recent transactions ── --}}
    <div class="bottom-grid">

        {{-- Spending by category --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Spending Breakdown</div>
                <a href="#" class="panel-action">See all</a>
            </div>
            @forelse($spendingByCategory as $cat)
            <div class="category-item">
                <div class="category-icon" style="background:{{ $cat['color'] }}22;">
                    💰
                </div>
                <div class="category-meta">
                    <div class="category-name">{{ $cat['name'] }}</div>
                    <div class="category-bar-wrap">
                        <div class="category-bar-fill" style="width:{{ min(100, $cat['pct'] * 2) }}%;background:{{ $cat['color'] }};"></div>
                    </div>
                </div>
                <div>
                    <div class="category-amount">${{ number_format($cat['amount'], 2) }}</div>
                    <div class="category-pct">{{ $cat['pct'] }}%</div>
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#aaa;text-align:center;padding:20px 0;">No expenses recorded this month.</p>
            @endforelse
        </div>

        {{-- Recent transactions --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Recent Transactions</div>
                <a href="#" class="panel-action">View all</a>
            </div>
            @forelse($recentTransactions as $tx)
            <div class="transaction-item">
                <div class="tx-icon" style="background:{{ $tx->type === 'Income' ? '#d1fae5' : '#fef3c7' }};">
                    {{ $tx->type === 'Income' ? '💰' : '💸' }}
                </div>
                <div class="tx-meta">
                    <div class="tx-name">{{ $tx->description }}</div>
                    <div class="tx-date">
                        {{ \Carbon\Carbon::parse($tx->date)->isToday() ? 'Today' : (\Carbon\Carbon::parse($tx->date)->isYesterday() ? 'Yesterday' : \Carbon\Carbon::parse($tx->date)->format('M j')) }}
                        @if($tx->category) · {{ $tx->category->name }} @endif
                    </div>
                </div>
                <div class="tx-amount {{ $tx->type === 'Income' ? 'credit' : 'debit' }}">
                    {{ $tx->type === 'Income' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#aaa;text-align:center;padding:20px 0;">No transactions yet.</p>
            @endforelse
        </div>
    </div>

</main>

{{-- ══════════ ADD TRANSACTION MODAL ══════════ --}}
<div id="add-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:200;align-items:center;justify-content:center;backdrop-filter:blur(4px);" onclick="if(event.target===this)closeModal()">
    <div style="background:#fff;border-radius:24px;padding:32px;width:100%;max-width:420px;position:relative;" onclick="event.stopPropagation()">
        <button onclick="closeModal()" style="position:absolute;top:20px;right:20px;background:none;border:none;font-size:20px;color:#aaa;cursor:pointer;">✕</button>
        <h2 style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;margin-bottom:6px;">Add Transaction</h2>
        <p style="font-size:13px;color:#aaa;margin-bottom:24px;">Log a new income or expense</p>

        <form method="POST" action="{{ route('transactions.store') }}" style="display:flex;flex-direction:column;gap:14px;">
            @csrf
            {{-- Type toggle — values match DB enum: 'Expense' / 'Income' --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                <label style="cursor:pointer;">
                    <input type="radio" name="type" value="Expense" checked style="display:none;">
                    <div class="type-btn" data-val="Expense" style="text-align:center;padding:11px;border-radius:14px;border:2px solid #FBCF97;background:#fff9f0;font-size:13px;font-weight:700;color:#9a5a10;cursor:pointer;" onclick="selectType('Expense')">💸 Expense</div>
                </label>
                <label style="cursor:pointer;">
                    <input type="radio" name="type" value="Income" style="display:none;">
                    <div class="type-btn" data-val="Income" style="text-align:center;padding:11px;border-radius:14px;border:2px solid #e5e7eb;background:#fff;font-size:13px;font-weight:700;color:#aaa;cursor:pointer;" onclick="selectType('Income')">💰 Income</div>
                </label>
            </div>

            <input type="number" name="amount" placeholder="Amount (e.g. 42.50)" step="0.01" min="0.01" required
                   style="padding:14px 18px;border-radius:14px;border:1.5px solid #ede9e1;font-size:15px;font-family:'Poppins',sans-serif;font-weight:700;outline:none;width:100%;transition:border-color 0.2s;"
                   onfocus="this.style.borderColor='#FBCF97'" onblur="this.style.borderColor='#ede9e1'">

            <input type="text" name="description" placeholder="Description (e.g. Grocery run)" required
                   style="padding:13px 18px;border-radius:14px;border:1.5px solid #ede9e1;font-size:14px;outline:none;width:100%;transition:border-color 0.2s;"
                   onfocus="this.style.borderColor='#FBCF97'" onblur="this.style.borderColor='#ede9e1'">

            {{-- category_id — populated from DB via $categories passed by DashboardController --}}
            <select name="category_id" required style="padding:13px 18px;border-radius:14px;border:1.5px solid #ede9e1;font-size:14px;color:#555;outline:none;width:100%;background:#fff;cursor:pointer;">
                <option value="">Select category</option>
                @foreach($categories ?? [] as $cat)
                    <option value="{{ data_get($cat, 'id') }}">{{ data_get($cat, 'name') }}</option>
                @endforeach
            </select>

            <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required
                   style="padding:13px 18px;border-radius:14px;border:1.5px solid #ede9e1;font-size:14px;outline:none;width:100%;transition:border-color 0.2s;"
                   onfocus="this.style.borderColor='#FBCF97'" onblur="this.style.borderColor='#ede9e1'">

            <button type="submit" class="quick-add-btn" style="margin-top:4px;">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Save Transaction
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal() {
        const m = document.getElementById('add-modal');
        m.style.display = 'flex';
    }

    function closeModal() {
        const m = document.getElementById('add-modal');
        m.style.display = 'none';
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    function selectType(val) {
        document.querySelectorAll('.type-btn').forEach(btn => {
            const isActive = btn.dataset.val === val;
            if (isActive) {
                btn.style.borderColor = val === 'Expense' ? '#FBCF97' : '#2EB872';
                btn.style.background  = val === 'Expense' ? '#fff9f0' : '#f0fdf4';
                btn.style.color       = val === 'Expense' ? '#9a5a10' : '#065f46';
            } else {
                btn.style.borderColor = '#e5e7eb';
                btn.style.background  = '#fff';
                btn.style.color       = '#aaa';
            }
        });
        document.querySelector(`input[name="type"][value="${val}"]`).checked = true;
    }
</script>
@endpush