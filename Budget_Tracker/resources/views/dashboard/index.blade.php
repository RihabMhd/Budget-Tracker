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

        h1, h2, h3, .font-display { font-family: 'Syne', sans-serif; }

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

        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.5px;
        }

        .topbar-subtitle {
            font-size: 14px;
            color: #888;
            margin-top: 2px;
            font-weight: 400;
        }

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

        .month-selector:hover {
            border-color: #FBCF97;
            box-shadow: 0 0 0 3px #fbcf9720;
        }

        /* ── Alerts ── */
        .alerts-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 24px;
        }

        .alert-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 500;
            border-left: 3px solid transparent;
            animation: fadeUp 0.3s ease both;
        }

        .alert-item.warn   { background: #fff9f0; border-left-color: #FBCF97; color: #7a4800; }
        .alert-item.danger { background: #fff2f2; border-left-color: #e05c5c; color: #9b1c1c; }
        .alert-item.info   { background: #f0fdf4; border-left-color: #2EB872; color: #065f46; }

        .alert-icon { font-size: 16px; flex-shrink: 0; }
        .alert-text strong { font-weight: 700; }

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
            animation: fadeUp 0.4s ease both;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.07);
        }

        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.10s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.20s; }

        .stat-card.accent-peach { background: #FBCF97; border-color: #FBCF97; }
        .stat-card.accent-green { background: #2EB872; border-color: #2EB872; }

        .stat-card.accent-peach .stat-label,
        .stat-card.accent-peach .stat-change { color: #8a5a1a; }
        .stat-card.accent-peach .stat-value  { color: #1C1C1E; }

        .stat-card.accent-green .stat-label,
        .stat-card.accent-green .stat-change { color: #0a5530; }
        .stat-card.accent-green .stat-value  { color: #fff; }

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
            stroke: #1C1C1E; fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }

        .stat-label {
            font-size: 12px;
            color: #999;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #1a1a1a;
            margin: 4px 0;
            letter-spacing: -1px;
        }

        .stat-change { font-size: 12px; font-weight: 500; }
        .stat-change.up   { color: #2EB872; }
        .stat-change.down { color: #e05c5c; }

        .stat-budget-bar-wrap { margin-top: 12px; }

        .stat-budget-bar-meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #8a5a1a;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .stat-budget-track {
            height: 5px;
            background: rgba(0,0,0,0.12);
            border-radius: 99px;
            overflow: hidden;
        }

        .stat-budget-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.9s ease;
        }

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
            animation: fadeUp 0.4s ease 0.25s both;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .panel-title {
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .panel-action {
            font-size: 12px;
            color: #2EB872;
            font-weight: 600;
            text-decoration: none;
        }

        .panel-action:hover { text-decoration: underline; }

        /* ── Bar Chart ── */
        .bar-chart {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            height: 140px;
            padding-bottom: 8px;
        }

        .bar-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            gap: 6px;
        }

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
            min-height: 2px;
        }

        .bar:hover { opacity: 0.8; }
        .bar.income  { background: #2EB872; }
        .bar.expense { background: #FBCF97; }

        .bar-label { font-size: 11px; color: #aaa; font-weight: 500; }
        .bar-label.current { color: #1a1a1a; font-weight: 700; }

        .chart-legend {
            display: flex;
            gap: 16px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f4f1eb;
            align-items: center;
        }

        .legend-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .legend-item {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            font-size: 12px;
            color: #888;
        }

        .chart-summary { margin-left: auto; font-size: 12px; color: #888; }

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
            background: rgba(251,207,151,0.12);
        }

        .goal-card::after {
            content: '';
            position: absolute;
            bottom: -40px; left: 20px;
            width: 90px; height: 90px;
            border-radius: 50%;
            background: rgba(46,184,114,0.10);
        }

        .goal-title {
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .goal-subtitle { font-size: 12px; color: #666; margin-bottom: 20px; }

        .goal-amounts {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 12px;
        }

        .goal-saved {
            font-family: 'Syne', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: #FBCF97;
        }

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

        /* ── Budgets panel ── */
        .budget-list { display: flex; flex-direction: column; gap: 16px; }

        .budget-item-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .budget-item-name {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .budget-color-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .budget-item-amounts { font-size: 13px; color: #1a1a1a; font-weight: 700; }
        .budget-item-amounts span { font-weight: 400; color: #aaa; }

        .budget-track-wrap {
            height: 6px;
            background: #f0ece4;
            border-radius: 99px;
            overflow: hidden;
        }

        .budget-track-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.6s ease;
        }

        .budget-item-pct { font-size: 11px; color: #aaa; text-align: right; margin-top: 3px; }

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

        /* ── Spending by category ── */
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

        .category-bar-fill { height: 100%; border-radius: 99px; transition: width 0.6s ease; }

        .category-amount {
            font-family: 'Syne', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #1a1a1a;
            text-align: right;
            white-space: nowrap;
        }

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
        .tx-amount.debit  { color: #1a1a1a; }

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
            display: flex; align-items: center; justify-content: center;
            gap: 8px;
        }

        .quick-add-btn:hover { background: #f7bc71; transform: translateY(-1px); }

        .quick-add-btn svg {
            width: 16px; height: 16px;
            stroke: #1C1C1E; fill: none;
            stroke-width: 2.5; stroke-linecap: round;
        }

        /* ── Modal ── */
        .hidden { display: none !important; }

        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 200;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
        }

        .modal-box {
            background: #fff;
            border-radius: 24px;
            padding: 32px;
            width: 100%; max-width: 440px;
            position: relative;
            animation: modalUp 0.25s ease;
        }

        @keyframes modalUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .modal-close {
            position: absolute; top: 20px; right: 20px;
            background: none; border: none;
            font-size: 20px; color: #aaa; cursor: pointer;
        }

        .modal-title { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; margin-bottom: 4px; }
        .modal-sub   { font-size: 13px; color: #aaa; margin-bottom: 24px; }

        .form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }

        .form-label { font-size: 12px; font-weight: 600; color: #666; }
        .form-error { font-size: 12px; color: #ef4444; }

        .form-input, .form-select {
            padding: 12px 16px;
            border-radius: 13px;
            border: 1.5px solid #ede9e1;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            outline: none; width: 100%; background: #fff;
            transition: border-color 0.2s;
        }

        .form-input:focus, .form-select:focus {
            border-color: #FBCF97;
            box-shadow: 0 0 0 3px rgba(251,207,151,0.2);
        }

        .type-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }

        .type-opt {
            text-align: center; padding: 11px;
            border-radius: 13px; border: 2px solid #e5e7eb;
            background: #fff; font-size: 13px; font-weight: 700;
            color: #aaa; cursor: pointer; transition: all 0.18s;
        }

        .active-expense { border-color: #FBCF97; background: #fff9f0; color: #9a5a10; }
        .active-income  { border-color: #2EB872; background: #f0fdf4; color: #065f46; }

        .submit-btn {
            width: 100%; background: #FBCF97; border: none;
            border-radius: 14px; padding: 14px;
            font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 700;
            color: #1C1C1E; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.2s; margin-top: 4px;
        }

        .submit-btn:hover { background: #f7bc71; transform: translateY(-1px); }
        .submit-btn svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .panel { animation: fadeUp 0.4s ease 0.25s both; }

        /* ── Responsive ── */
        @media (max-width: 1200px) {
            .stats-grid  { grid-template-columns: repeat(2, 1fr); }
            .mid-grid    { grid-template-columns: 1fr; }
            .bottom-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 24px 20px; }
        }
    </style>
@endpush

@section('content')

    <main class="main-content">

        {{-- ── Top bar ── --}}
        <div class="topbar">
            <div>
                @php
                    $hour = now()->hour;
                    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
                @endphp
                <div class="topbar-title">{{ $greeting }}, {{ Auth::user()->name ?? Auth::user()->username }}</div>
                <div class="topbar-subtitle">Here's your financial snapshot for {{ $selectedMonth->format('F Y') }}.</div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                {{-- Month switcher --}}
                <div style="display:flex;align-items:center;gap:0;background:#fff;border:1px solid #e8e4dc;border-radius:14px;overflow:hidden;">
                    <a href="{{ route('dashboard') }}?month={{ $prevMonth }}"
                       style="display:flex;align-items:center;padding:10px 14px;color:#888;text-decoration:none;transition:all 0.2s;border-right:1px solid #e8e4dc;"
                       onmouseover="this.style.background='#F5F3EE'" onmouseout="this.style.background='transparent'">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;"><polyline points="15,18 9,12 15,6"/></svg>
                    </a>
                    <span style="padding:10px 16px;font-size:14px;font-weight:600;color:#1a1a1a;white-space:nowrap;">
                        {{ $selectedMonth->format('F Y') }}
                    </span>
                    @if(!$isCurrentMonth)
                        <a href="{{ route('dashboard') }}?month={{ $nextMonth }}"
                           style="display:flex;align-items:center;padding:10px 14px;color:#888;text-decoration:none;transition:all 0.2s;border-left:1px solid #e8e4dc;"
                           onmouseover="this.style.background='#F5F3EE'" onmouseout="this.style.background='transparent'">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;"><polyline points="9,18 15,12 9,6"/></svg>
                        </a>
                    @else
                        <span style="display:flex;align-items:center;padding:10px 14px;color:#ddd;border-left:1px solid #e8e4dc;cursor:not-allowed;">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;"><polyline points="9,18 15,12 9,6"/></svg>
                        </span>
                    @endif
                </div>
                <button class="quick-add-btn" style="width:auto;padding:13px 20px;" onclick="openModal('add-modal')">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Transaction
                </button>
            </div>
        </div>

        {{-- ── Streak ── --}}
        @if(Auth::user()->current_streak > 0)
            <div class="streak-row">
                <span class="streak-flame">🔥</span>
                <span class="streak-text">You're on a <span>{{ Auth::user()->current_streak }}-day streak</span> — keep logging your expenses!</span>
            </div>
        @endif

        {{-- ── Smart Alerts ── --}}
        @php
            $alerts = [];
            foreach ($budgets as $b) {
                if ($b->percent_used >= 100)
                    $alerts[] = ['type' => 'danger', 'icon' => '🚨', 'msg' => "<strong>{$b->category->name}</strong> budget exceeded — spent \$" . number_format($b->current_spending, 2) . " of \$" . number_format($b->monthly_limit, 2)];
                elseif ($b->percent_used >= 80)
                    $alerts[] = ['type' => 'warn', 'icon' => '⚠️', 'msg' => "<strong>{$b->category->name}</strong> is at {$b->percent_used}% of your budget"];
            }
            if ($savingsRate < 10 && $monthlyIncome > 0)
                $alerts[] = ['type' => 'warn', 'icon' => '📉', 'msg' => "Your savings rate is low at <strong>{$savingsRate}%</strong> this month"];
            if ($savingsRate >= 20 && $monthlyIncome > 0)
                $alerts[] = ['type' => 'info', 'icon' => '🎉', 'msg' => "Great job — you're saving <strong>{$savingsRate}%</strong> of your income this month!"];
        @endphp
        @if(count($alerts))
            <div class="alerts-stack">
                @foreach($alerts as $alert)
                    <div class="alert-item {{ $alert['type'] }}">
                        <span class="alert-icon">{{ $alert['icon'] }}</span>
                        <span class="alert-text">{!! $alert['msg'] !!}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── Stat Cards ── --}}
        <div class="stats-grid">

            {{-- Available to Spend --}}
            <div class="stat-card accent-peach">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                </div>
                <div class="stat-label">Available to Spend</div>
                <div class="stat-value" style="color:{{ $remainingWallet < 0 ? '#9b1c1c' : '#1C1C1E' }};">
                    ${{ number_format($remainingWallet, 2) }}
                </div>
                <div class="stat-budget-bar-wrap">
                    <div class="stat-budget-bar-meta">
                        <span>{{ round($spentPercentage) }}% spent</span>
                        <span>${{ number_format($startingAllowance, 2) }}</span>
                    </div>
                    <div class="stat-budget-track">
                        <div class="stat-budget-fill" style="width:{{ min(100, $spentPercentage) }}%; background:{{ $spentPercentage > 90 ? '#9b1c1c' : '#a86200' }};"></div>
                    </div>
                </div>
                <div class="stat-bg-decoration"></div>
            </div>

            {{-- Total Balance --}}
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                </div>
                <div class="stat-label">Total Balance</div>
                <div class="stat-value" style="{{ $totalBalance < 0 ? 'color:#e05c5c;' : '' }}">
                    ${{ number_format($totalBalance, 2) }}
                </div>
                <div class="stat-change {{ $totalBalance >= 0 ? 'up' : 'down' }}">
                    {{ $totalBalance >= 0 ? '↑ Positive balance' : '↓ Negative balance' }}
                </div>
                <div class="stat-bg-decoration"></div>
            </div>

            {{-- Monthly Income --}}
            <div class="stat-card accent-green">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/>
                        <polyline points="17,6 23,6 23,12"/>
                    </svg>
                </div>
                <div class="stat-label">Income</div>
                <div class="stat-value">${{ number_format($monthlyIncome, 2) }}</div>
                <div class="stat-change" style="color:#a8f0cc;">↑ This month</div>
                <div class="stat-bg-decoration"></div>
            </div>

            {{-- Monthly Expenses --}}
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <polyline points="23,18 13.5,8.5 8.5,13.5 1,6"/>
                        <polyline points="17,18 23,18 23,12"/>
                    </svg>
                </div>
                <div class="stat-label">Expenses</div>
                <div class="stat-value">${{ number_format($monthlyExpenses, 2) }}</div>
                <div class="stat-change {{ $savingsRate >= 20 ? 'up' : 'down' }}">
                    {{ $savingsRate }}% savings rate
                </div>
            </div>

        </div>

        {{-- ── Mid grid: chart + right column ── --}}
        <div class="mid-grid">

            {{-- Income vs Expenses bar chart --}}
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">Income vs Expenses</div>
                    <a href="#" class="panel-action">9-month view</a>
                </div>
                @php $maxVal = max(1, max(array_merge($chartIncomes, $chartExpenses))); @endphp
                <div class="bar-chart">
                    @foreach($chartMonths as $i => $month)
                        @php
                            $incH   = max(2, round(($chartIncomes[$i]  / $maxVal) * 110));
                            $expH   = max(2, round(($chartExpenses[$i] / $maxVal) * 110));
                            $isLast = $i === count($chartMonths) - 1;
                        @endphp
                        <div class="bar-group">
                            <div class="bar-wrap">
                                <div class="bar income"  style="height:{{ $incH }}px; opacity:{{ $isLast ? 1 : 0.45 }};"></div>
                                <div class="bar expense" style="height:{{ $expH }}px; opacity:{{ $isLast ? 1 : 0.45 }};"></div>
                            </div>
                            <div class="bar-label {{ $isLast ? 'current' : '' }}">{{ $month }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="chart-legend">
                    <div class="legend-item"><div class="legend-dot" style="background:#2EB872;margin-top:4px;"></div> Income</div>
                    <div class="legend-item"><div class="legend-dot" style="background:#FBCF97;margin-top:4px;"></div> Expenses</div>
                    <div class="chart-summary">
                        This month:
                        <span style="color:#2EB872;font-weight:700;">+${{ number_format($monthlyIncome, 0) }}</span>
                        /
                        <span style="color:#e07a10;font-weight:700;">-${{ number_format($monthlyExpenses, 0) }}</span>
                    </div>
                </div>
            </div>

            {{-- Right column --}}
            <div style="display:flex;flex-direction:column;gap:18px;">

                {{-- Savings Goal --}}
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
                                <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#888;">
                                    ${{ number_format($goalTarget) }}
                                </div>
                            </div>
                        </div>
                        <div class="goal-progress-wrap">
                            <div class="goal-progress-fill" style="width:{{ $goalPct }}%;"></div>
                        </div>
                        <div class="goal-pct">
                            <strong>{{ $goalPct }}% complete</strong> — ${{ number_format($goalTarget - $goalSaved) }} to go
                        </div>
                    @else
                        <div class="goal-title">🎯 No active goal</div>
                        <div class="goal-subtitle" style="margin-bottom:20px;">Set a savings goal to track your progress here.</div>
                        @if(\Illuminate\Support\Facades\Route::has('goals.create'))
                            <a href="{{ route('goals.create') }}" style="display:inline-block;background:#FBCF97;color:#1C1C1E;font-family:'Syne',sans-serif;font-weight:700;font-size:13px;padding:10px 18px;border-radius:12px;text-decoration:none;">
                                + Create a Goal
                            </a>
                        @endif
                    @endif
                </div>

                {{-- Monthly Budgets --}}
                <div class="panel" style="padding:22px 24px;">
                    <div class="panel-header">
                        <div class="panel-title">Monthly Budgets</div>
                        @if(\Illuminate\Support\Facades\Route::has('budgets.index'))
                            <a href="{{ route('budgets.index') }}" class="panel-action">Manage →</a>
                        @endif
                    </div>
                    <div class="budget-list">
                        @forelse($budgets as $budget)
                            <div>
                                <div class="budget-item-head">
                                    <div class="budget-item-name">
                                        <div class="budget-color-dot" style="background:{{ $budget->category->color ?? '#FBCF97' }};"></div>
                                        {{ $budget->category->name }}
                                    </div>
                                    <div class="budget-item-amounts">
                                        ${{ number_format($budget->current_spending, 2) }}
                                        <span>/ ${{ number_format($budget->monthly_limit, 2) }}</span>
                                    </div>
                                </div>
                                <div class="budget-track-wrap">
                                    <div class="budget-track-fill" style="
                                        width:{{ min(100, $budget->percent_used) }}%;
                                        background:{{ $budget->percent_used >= 100 ? '#e05c5c' : ($budget->percent_used >= 80 ? '#FBCF97' : ($budget->category->color ?? '#2EB872')) }};
                                    "></div>
                                </div>
                                <div class="budget-item-pct">{{ $budget->percent_used }}% used</div>
                            </div>
                        @empty
                            <p style="font-size:13px;color:#aaa;text-align:center;padding:20px 0;">No budgets set up yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Bottom: Spending breakdown + Recent transactions ── --}}
        <div class="bottom-grid">

            {{-- Spending by category --}}
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">Spending Breakdown</div>
                    <span style="font-size:12px;color:#aaa;">{{ $selectedMonth->format('M Y') }}</span>
                </div>
                @forelse($spendingByCategory as $cat)
                    <div class="category-item">
                        <div class="category-icon" style="background:{{ $cat['color'] }}22;">💰</div>
                        <div class="category-meta">
                            <div class="category-name">{{ $cat['name'] }}</div>
                            <div class="category-bar-wrap">
                                <div class="category-bar-fill" style="width:{{ min(100, $cat['pct']) }}%;background:{{ $cat['color'] }};"></div>
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

            {{-- Recent Transactions --}}
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">Recent Transactions</div>
                    @if(\Illuminate\Support\Facades\Route::has('transactions.index'))
                        <a href="{{ route('transactions.index') }}" class="panel-action">View all</a>
                    @endif
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
                            {{ $tx->type === 'Income' ? '+' : '−' }}${{ number_format($tx->amount, 2) }}
                        </div>
                    </div>
                @empty
                    <p style="font-size:13px;color:#aaa;text-align:center;padding:20px 0;">No transactions yet.</p>
                @endforelse
            </div>

        </div>

    </main>

    {{-- ══ ADD TRANSACTION MODAL ══ --}}
    <div id="add-modal" class="modal-overlay hidden" onclick="if(event.target===this)closeModal('add-modal')">
        <div class="modal-box" onclick="event.stopPropagation()">
            <button class="modal-close" onclick="closeModal('add-modal')">✕</button>
            <div class="modal-title">Add Transaction</div>
            <div class="modal-sub">Log a new income or expense</div>

            <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <span class="form-label">Type</span>
                    <div class="type-grid">
                        <div class="type-opt active-expense" id="opt-expense" onclick="setType('Expense')">💸 Expense</div>
                        <div class="type-opt" id="opt-income" onclick="setType('Income')">💰 Income</div>
                    </div>
                    <input type="hidden" name="type" id="type-input" value="Expense">
                    @error('type')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Amount ($)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00"
                        value="{{ old('amount') }}" class="form-input" required>
                    @error('amount')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" placeholder="e.g. Grocery run"
                        value="{{ old('description') }}" class="form-input" required>
                    @error('description')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select a category</option>
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}"
                        class="form-input" required>
                    @error('date')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Receipt Image (optional)</label>
                    <input type="file" name="receipt_image" accept="image/*" class="form-input" style="padding:8px 14px;">
                    @error('receipt_image')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="submit-btn">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Save Transaction
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal('add-modal'); });

        function setType(val) {
            document.getElementById('type-input').value = val;
            document.getElementById('opt-expense').className = 'type-opt' + (val === 'Expense' ? ' active-expense' : '');
            document.getElementById('opt-income').className  = 'type-opt' + (val === 'Income'  ? ' active-income'  : '');
        }
    </script>
@endpush