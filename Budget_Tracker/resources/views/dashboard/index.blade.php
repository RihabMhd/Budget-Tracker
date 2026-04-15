@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/dashboard.css'])
@endpush

@section('content')

    <main class="main-content">

        {{-- Reopen add modal automatically if validation failed --}}
        @if ($errors->any())
            <span data-reopen-modal="add-modal" style="display:none;"></span>
        @endif

        {{-- ── Top bar ── --}}
        <div class="topbar">
            <div>
                @php
                    $hour = now()->hour;
                    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
                @endphp
                <div class="topbar-title">{{ $greeting }}, {{ Auth::user()->name ?? Auth::user()->username }}</div>
                <div class="topbar-subtitle">Here's your spending snapshot for {{ $selectedMonth->format('F Y') }}.</div>
            </div>

            {{-- Topbar actions: month switcher + buttons --}}
            <div class="topbar-actions">
                {{-- Month switcher --}}
                <div style="display:flex;align-items:center;gap:0;background:#fff;border:1px solid #e8e4dc;border-radius:14px;overflow:hidden;">
                    <a href="{{ route('dashboard') }}?month={{ $prevMonth }}"
                        style="display:flex;align-items:center;padding:10px 14px;color:#888;text-decoration:none;transition:all 0.2s;border-right:1px solid #e8e4dc;"
                        onmouseover="this.style.background='#F5F3EE'" onmouseout="this.style.background='transparent'">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;">
                            <polyline points="15,18 9,12 15,6" />
                        </svg>
                    </a>
                    <span style="padding:10px 16px;font-size:14px;font-weight:600;color:#1a1a1a;white-space:nowrap;">
                        {{ $selectedMonth->format('F Y') }}
                    </span>
                    @if (!$isCurrentMonth)
                        <a href="{{ route('dashboard') }}?month={{ $nextMonth }}"
                            style="display:flex;align-items:center;padding:10px 14px;color:#888;text-decoration:none;transition:all 0.2s;border-left:1px solid #e8e4dc;"
                            onmouseover="this.style.background='#F5F3EE'" onmouseout="this.style.background='transparent'">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;">
                                <polyline points="9,18 15,12 9,6" />
                            </svg>
                        </a>
                    @else
                        <span style="display:flex;align-items:center;padding:10px 14px;color:#ddd;border-left:1px solid #e8e4dc;cursor:not-allowed;">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;">
                                <polyline points="9,18 15,12 9,6" />
                            </svg>
                        </span>
                    @endif
                </div>

                <button class="quick-add-btn" style="width:auto;padding:13px 20px;" data-open-modal="add-modal">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Expense
                </button>

                <a href="{{ route('export.report', ['month' => $selectedMonth->format('Y-m')]) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                    style="white-space:nowrap;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>

        {{-- ── Smart Alerts ── --}}
        @php
            $alerts = [];
            foreach ($budgets as $b) {
                if ($b['percent_used'] >= 100) {
                    $alerts[] = [
                        'type' => 'danger',
                        'icon' => '🚨',
                        'msg' =>
                            "<strong>{$b['category']->name}</strong> budget exceeded — spent " .
                            number_format($b['current_spending'], 2) .
                            " DH of " .
                            number_format($b['monthly_limit'], 2) . " DH",
                    ];
                } elseif ($b['percent_used'] >= 80) {
                    $alerts[] = [
                        'type' => 'warn',
                        'icon' => '⚠️',
                        'msg' => "<strong>{$b['category']->name}</strong> is at {$b['percent_used']}% of your budget",
                    ];
                }
            }
            if ($spentPercentage >= 90) {
                $alerts[] = [
                    'type' => 'danger',
                    'icon' => '🚨',
                    'msg' => "You've used <strong>" . round($spentPercentage) . '%</strong> of your monthly allowance!',
                ];
            } elseif ($spentPercentage >= 70) {
                $alerts[] = [
                    'type' => 'warn',
                    'icon' => '⚠️',
                    'msg' => "You've used <strong>" . round($spentPercentage) . '%</strong> of your monthly allowance',
                ];
            } elseif ($spentPercentage <= 30 && $monthlyAllowance > 0) {
                $alerts[] = [
                    'type' => 'info',
                    'icon' => '🎉',
                    'msg' =>
                        "Great job — you've only spent <strong>" .
                        round($spentPercentage) .
                        '%</strong> of your allowance this month!',
                ];
            }
        @endphp
        @if (count($alerts))
            <div class="alerts-stack">
                @foreach ($alerts as $alert)
                    <div class="alert-item {{ $alert['type'] }}">
                        <span class="alert-icon">{{ $alert['icon'] }}</span>
                        <span class="alert-text">{!! $alert['msg'] !!}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── Stat Cards ── --}}
        <div class="stats-grid">

            {{-- Monthly Allowance --}}
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" />
                        <path d="M2 10h20" />
                    </svg>
                </div>
                <div class="stat-label">Monthly Allowance</div>
                <div class="stat-value">{{ number_format($monthlyAllowance, 2) }} DH</div>
                <div class="stat-change up">↻ Resets every month</div>
                <div class="stat-bg-decoration"></div>
            </div>

            {{-- Spent This Month --}}
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <polyline points="23,18 13.5,8.5 8.5,13.5 1,6" />
                        <polyline points="17,18 23,18 23,12" />
                    </svg>
                </div>
                <div class="stat-label">Spent This Month</div>
                <div class="stat-value">{{ number_format($monthlyExpenses, 2) }} DH</div>
                <div class="stat-change {{ $spentPercentage >= 80 ? 'down' : 'up' }}">
                    {{ round($spentPercentage) }}% of allowance used
                </div>
            </div>

            {{-- Remaining Budget --}}
            <div class="stat-card accent-peach">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" />
                        <path d="M2 10h20" />
                    </svg>
                </div>
                <div class="stat-label">Remaining This Month</div>
                <div class="stat-value" style="color:{{ $remaining < 0 ? '#9b1c1c' : '#1C1C1E' }};">
                    {{ $remaining < 0 ? '-' : '' }}{{ number_format(abs($remaining), 2) }} DH
                </div>
                <div class="stat-budget-bar-wrap">
                    <div class="stat-budget-bar-meta">
                        <span>{{ round($spentPercentage) }}% spent</span>
                        <span>of {{ number_format($monthlyAllowance, 2) }} DH</span>
                    </div>
                    <div class="stat-budget-track">
                        <div class="stat-budget-fill"
                            style="width:{{ min(100, $spentPercentage) }}%; background:{{ $spentPercentage > 90 ? '#9b1c1c' : '#a86200' }};">
                        </div>
                    </div>
                </div>
                <div class="stat-bg-decoration"></div>
            </div>

            {{-- Daily Safe-to-Spend --}}
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12,6 12,12 16,14" />
                    </svg>
                </div>
                <div class="stat-label">Daily Safe-to-Spend</div>
                <div class="stat-value" style="color:#2EB872;">{{ number_format($dailySafeToSpend, 2) }} DH</div>
                <div class="stat-change up">Based on days remaining this month</div>
                <div class="stat-bg-decoration"></div>
            </div>

            {{-- Logging Streak --}}
            <div class="stat-card">
                <div class="stat-icon" style="font-size:20px;">🔥</div>
                <div class="stat-label">Logging Streak</div>
                <div class="stat-value">{{ Auth::user()->current_streak }} days</div>
                @if (Auth::user()->current_streak >= 4)
                    <div class="stat-change up" style="color:#2EB872;">↑ +20 pts daily bonus active!</div>
                @else
                    <div class="stat-change">Reach 4 days for bonus points</div>
                @endif
            </div>

        </div>

        {{-- ── Mid grid: chart + right column ── --}}
        <div class="mid-grid-auto">

            {{-- LEFT: Spending bar chart --}}
            <div class="panel" style="padding:22px 24px;">
                <div class="panel-header">
                    <div class="panel-title">Monthly Spending</div>
                    <a href="#" class="panel-action">9-month view</a>
                </div>
                @php $maxVal = max(1, max(array_merge($chartExpenses, $chartAllowances))); @endphp
                <div class="bar-chart" style="height:130px;align-items:flex-end;">
                    @foreach ($chartMonths as $i => $month)
                        @php
                            $expH = max(4, round(($chartExpenses[$i] / $maxVal) * 110));
                            $isLast = $i === count($chartMonths) - 1;
                        @endphp
                        <div class="bar-group">
                            <div class="bar-wrap">
                                <div class="bar expense"
                                    style="height:{{ $expH }}px; opacity:{{ $isLast ? 1 : 0.45 }};"></div>
                            </div>
                            <div class="bar-label {{ $isLast ? 'current' : '' }}">{{ $month }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="chart-legend" style="margin-top:12px;">
                    <div class="legend-item">
                        <div class="legend-dot" style="background:#FBCF97;"></div> Expenses
                    </div>
                    <div class="chart-summary">
                        This month:
                        <span style="color:#e07a10;font-weight:700;">−{{ number_format($monthlyExpenses, 0) }} DH</span>
                        /
                        <span style="color:#888;font-weight:700;">{{ number_format($monthlyAllowance, 0) }} DH allowance</span>
                    </div>
                </div>

                {{-- ── Daily Spending Heatmap ── --}}
                @php
                    $dailyTotals = [];
                    foreach ($recentTransactions as $tx) {
                        $day = (int) \Carbon\Carbon::parse($tx->date)->format('j');
                        $dailyTotals[$day] = ($dailyTotals[$day] ?? 0) + $tx->amount;
                    }
                    $maxDay      = count($dailyTotals) ? max($dailyTotals) : 1;
                    $daysInMonth = (int) $selectedMonth->format('t');
                    $firstDow    = (int) \Carbon\Carbon::parse($selectedMonth->format('Y-m-01'))->format('N');
                    $firstDow    = $firstDow % 7;
                    $today       = $isCurrentMonth ? (int) now()->format('j') : $daysInMonth;
                @endphp

                <div style="margin-top:22px;border-top:1px solid #f4f1eb;padding-top:18px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
                        <span style="font-size:12px;font-weight:700;color:#1a1a1a;font-family:'Syne',sans-serif;">Daily Activity</span>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:11px;color:#bbb;">Less</span>
                            @foreach([0.1, 0.3, 0.55, 0.8, 1] as $op)
                                <div style="width:10px;height:10px;border-radius:3px;background:#FBCF97;opacity:{{ $op }};"></div>
                            @endforeach
                            <span style="font-size:11px;color:#bbb;">More</span>
                        </div>
                    </div>

                    {{-- Day-of-week headers --}}
                    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:4px;">
                        @foreach(['S','M','T','W','T','F','S'] as $wd)
                            <div style="text-align:center;font-size:10px;color:#bbb;font-weight:600;">{{ $wd }}</div>
                        @endforeach
                    </div>

                    {{-- Calendar cells --}}
                    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:3px;">
                        @for($e = 0; $e < $firstDow; $e++)
                            <div style="height:28px;"></div>
                        @endfor

                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $amt      = $dailyTotals[$d] ?? 0;
                                $isFuture = $d > $today;
                                $intensity = $amt > 0 ? max(0.15, min(1, $amt / $maxDay)) : 0;
                                if ($isFuture) {
                                    $bg     = '#f4f1eb';
                                    $color  = '#ddd';
                                    $border = '1px solid transparent';
                                } elseif ($amt > 0) {
                                    $bg     = 'rgba(251,207,151,' . round($intensity, 2) . ')';
                                    $color  = $intensity > 0.6 ? '#7a4800' : '#b06a00';
                                    $border = '1px solid rgba(251,207,151,0.4)';
                                } else {
                                    $bg     = '#f9f8f5';
                                    $color  = '#ccc';
                                    $border = '1px solid #ede9e1';
                                }
                                $isToday = $isCurrentMonth && $d === $today;
                            @endphp
                            <div title="{{ $amt > 0 ? number_format($amt,2).' DH on '.$selectedMonth->format('M').' '.$d : 'No spending' }}"
                                style="height:28px;border-radius:5px;background:{{ $bg }};border:{{ $isToday ? '2px solid #e07a10' : $border }};display:flex;align-items:center;justify-content:center;cursor:default;transition:transform 0.15s;"
                                onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                <span style="font-size:9px;font-weight:700;color:{{ $color }};line-height:1;">{{ $d }}</span>
                            </div>
                        @endfor
                    </div>

                    {{-- Mini stats row --}}
                    @php
                        $activeDays = count($dailyTotals);
                        $avgPerDay  = $activeDays > 0 ? $monthlyExpenses / $activeDays : 0;
                        $peakDay    = $dailyTotals ? array_search(max($dailyTotals), $dailyTotals) : null;
                    @endphp
                    <div class="heatmap-stats-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:14px;">
                        <div style="background:#f9f8f5;border-radius:10px;padding:10px 12px;text-align:center;">
                            <div style="font-size:13px;font-weight:800;font-family:'Syne',sans-serif;color:#1a1a1a;">{{ $activeDays }}</div>
                            <div style="font-size:10px;color:#aaa;margin-top:1px;">Active days</div>
                        </div>
                        <div style="background:#f9f8f5;border-radius:10px;padding:10px 12px;text-align:center;">
                            <div style="font-size:13px;font-weight:800;font-family:'Syne',sans-serif;color:#e07a10;">{{ number_format($avgPerDay, 0) }} DH</div>
                            <div style="font-size:10px;color:#aaa;margin-top:1px;">Avg / active day</div>
                        </div>
                        <div style="background:#f9f8f5;border-radius:10px;padding:10px 12px;text-align:center;">
                            <div style="font-size:13px;font-weight:800;font-family:'Syne',sans-serif;color:#1a1a1a;">{{ $peakDay ? $selectedMonth->format('M').' '.$peakDay : '—' }}</div>
                            <div style="font-size:10px;color:#aaa;margin-top:1px;">Peak day</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT column: Savings Goal + Category Budgets --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Savings Goal --}}
                <div class="goal-card" style="margin:0;">
                    @if ($goal)
                        <div class="goal-title">🎯 {{ $goalTitle }}</div>
                        <div class="goal-subtitle">{{ $goalDeadline ? 'Target: ' . $goalDeadline : 'No deadline set' }}</div>
                        <div class="goal-amounts">
                            <div>
                                <div style="font-size:11px;color:#555;margin-bottom:2px;">SAVED SO FAR</div>
                                <div class="goal-saved">{{ number_format($goalSaved) }} DH</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:11px;color:#555;margin-bottom:2px;">TARGET</div>
                                <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#888;">
                                    {{ number_format($goalTarget) }} DH
                                </div>
                            </div>
                        </div>
                        <div class="goal-progress-wrap">
                            <div class="goal-progress-fill" style="width:{{ $goalPct }}%;"></div>
                        </div>
                        <div class="goal-pct">
                            <strong>{{ $goalPct }}% complete</strong> —
                            {{ number_format($goalTarget - $goalSaved) }} DH to go
                        </div>
                    @else
                        <div class="goal-title">🎯 No active goal</div>
                        <div class="goal-subtitle" style="margin-bottom:16px;">Set a savings goal to track your progress here.</div>
                        @if (\Illuminate\Support\Facades\Route::has('goals.index'))
                            <a href="{{ route('goals.index') }}"
                                style="display:inline-block;background:#FBCF97;color:#1C1C1E;font-family:'Syne',sans-serif;font-weight:700;font-size:13px;padding:10px 18px;border-radius:12px;text-decoration:none;">
                                + Create a Goal
                            </a>
                        @endif
                    @endif
                </div>

                {{-- Category Budgets --}}
                <div class="panel" style="padding:20px 22px;">
                    <div class="panel-header">
                        <div class="panel-title">Category Budgets</div>
                        @if (\Illuminate\Support\Facades\Route::has('budgets.index'))
                            <a href="{{ route('budgets.index') }}" class="panel-action">Manage →</a>
                        @endif
                    </div>
                    <div class="budget-list">
                        @forelse($budgets as $budget)
                            <div style="margin-bottom:14px;">
                                <div class="budget-item-head">
                                    <div class="budget-item-name">
                                        <div class="budget-color-dot"
                                            style="background:{{ $budget['category']->color ?? '#FBCF97' }};"></div>
                                        {{ $budget['category']->name }}
                                    </div>
                                    <div class="budget-item-amounts">
                                        {{ number_format($budget['current_spending'], 2) }} DH
                                        <span>/ {{ number_format($budget['monthly_limit'], 2) }} DH</span>
                                    </div>
                                </div>
                                <div class="budget-track-wrap">
                                    <div class="budget-track-fill"
                                        style="width:{{ min(100, $budget['percent_used']) }}%;background:{{ $budget['percent_used'] >= 100 ? '#e05c5c' : ($budget['percent_used'] >= 80 ? '#FBCF97' : $budget['category']->color ?? '#2EB872') }};">
                                    </div>
                                </div>
                                <div class="budget-item-pct">{{ $budget['percent_used'] }}% used</div>
                            </div>
                        @empty
                            <p style="font-size:13px;color:#aaa;text-align:center;padding:16px 0;">No budgets set up yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Bottom: Spending breakdown + Recent expenses ── --}}
        <div class="bottom-grid" style="margin-top:20px;">

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
                                <div class="category-bar-fill"
                                    style="width:{{ min(100, $cat['pct']) }}%;background:{{ $cat['color'] }};"></div>
                            </div>
                        </div>
                        <div>
                            <div class="category-amount">{{ number_format($cat['amount'], 2) }} DH</div>
                            <div class="category-pct">{{ $cat['pct'] }}%</div>
                        </div>
                    </div>
                @empty
                    <p style="font-size:13px;color:#aaa;text-align:center;padding:20px 0;">No expenses recorded this month.</p>
                @endforelse
            </div>

            {{-- Recent Expenses --}}
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">Recent Expenses</div>
                    @if (\Illuminate\Support\Facades\Route::has('transactions.index'))
                        <a href="{{ route('transactions.index') }}" class="panel-action">View all</a>
                    @endif
                </div>
                @forelse($recentTransactions as $tx)
                    <div class="transaction-item">
                        <div class="tx-icon" style="background:#fef3c7;">💸</div>
                        <div class="tx-meta">
                            <div class="tx-name">{{ $tx->description ?? '—' }}</div>
                            <div class="tx-date">
                                {{ \Carbon\Carbon::parse($tx->date)->isToday() ? 'Today' : (\Carbon\Carbon::parse($tx->date)->isYesterday() ? 'Yesterday' : \Carbon\Carbon::parse($tx->date)->format('M j')) }}
                                @if ($tx->category)
                                    · {{ $tx->category->name }}
                                @endif
                            </div>
                        </div>
                        <div class="tx-amount debit">
                            −{{ number_format($tx->amount, 2) }} DH
                        </div>
                    </div>
                @empty
                    <p style="font-size:13px;color:#aaa;text-align:center;padding:20px 0;">No expenses yet.</p>
                @endforelse
            </div>

        </div>

    </main>

    {{-- ══ ADD EXPENSE MODAL ══ --}}
    <div id="add-modal" class="modal-overlay hidden" data-dismiss-modal="add-modal">
        <div class="modal-box" onclick="event.stopPropagation()">
            <button class="modal-close" data-close-modal="add-modal">✕</button>
            <div class="modal-title">Add Expense</div>
            <div class="modal-sub">Log a new expense from your monthly allowance</div>

            <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="Expense">

                <div class="form-group">
                    <label class="form-label">Amount (DH)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00"
                        value="{{ old('amount') }}" class="form-input" required>
                    @error('amount')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" placeholder="e.g. Grocery run"
                        value="{{ old('description') }}" class="form-input">
                    @error('description')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select a category</option>
                        @foreach ($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}"
                        class="form-input" required>
                    @error('date')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Receipt Image (optional)</label>
                    <input type="file" name="receipt_image" accept="image/*" class="form-input"
                        style="padding:8px 14px;">
                    @error('receipt_image')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Save Expense
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    @vite('resources/js/dashboard.js')
@endpush