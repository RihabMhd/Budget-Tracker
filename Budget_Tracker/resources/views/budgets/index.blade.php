@extends('layouts.app')

@section('title', 'Budgets')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/budgets.css'])
@endpush

@section('content')
<main class="main-content">

    @if(session('success'))
    <div class="flash">✅ {{ session('success') }}</div>
    @endif

    <div class="page-header">
        <div>
            <div class="page-title">Budgets</div>
            <div class="page-sub">Set limits for the whole month and per category — {{ now()->format('F Y') }}</div>
        </div>
    </div>

    @php
        $monthlyPct    = $hasMonthly && $monthlyLimit > 0
                         ? min(100, round(($totalSpentThisMonth / $monthlyLimit) * 100))
                         : 0;
        $remaining     = $hasMonthly ? $monthlyLimit - $totalSpentThisMonth : null;
        $catBudgetsSet = $budgets->count();
        $isOver        = $hasMonthly && $remaining < 0;
    @endphp

    {{-- Overview cards --}}
    <div class="overview-grid">

        <div class="ov-card dark">
            <div class="ov-label">Spent This Month</div>
            <div class="ov-value">${{ number_format($totalSpentThisMonth, 2) }}</div>
            <div class="ov-sub">
                @if($hasMonthly) of ${{ number_format($monthlyLimit, 2) }} budget
                @else No overall limit set @endif
            </div>
            @if($hasMonthly)
            @php $r = 28; $circ = round(2 * M_PI * $r, 2); $offset = round($circ * (1 - $monthlyPct / 100), 2); @endphp
            <div class="ov-ring-wrap" style="width:72px;height:72px;">
                <svg width="72" height="72" class="ov-ring" viewBox="0 0 72 72">
                    <circle class="ov-ring-bg"  cx="36" cy="36" r="{{ $r }}"/>
                    <circle class="ov-ring-fill" cx="36" cy="36" r="{{ $r }}"
                        stroke="{{ $monthlyPct >= 90 ? '#f87171' : '#FBCF97' }}"
                        stroke-dasharray="{{ $circ }}"
                        stroke-dashoffset="{{ $offset }}"/>
                </svg>
                <div class="ov-ring-pct" style="color:#FBCF97;">{{ $monthlyPct }}%</div>
            </div>
            @endif
        </div>

        <div class="ov-card {{ $isOver ? 'red' : ($hasMonthly ? 'green' : '') }}">
            <div class="ov-label">
                @if(!$hasMonthly) Monthly Limit
                @elseif($isOver)  Over Budget
                @else             Remaining
                @endif
            </div>
            @if($hasMonthly)
                <div class="ov-value">${{ number_format(abs($remaining ?? 0), 2) }}</div>
                <div class="ov-sub">{{ $isOver ? 'over your monthly limit' : 'left for the month' }}</div>
            @else
                <div class="ov-value">—</div>
                <div class="ov-sub">Set a monthly limit below</div>
            @endif
        </div>

        <div class="ov-card">
            <div class="ov-label">Category Budgets</div>
            <div class="ov-value">{{ $catBudgetsSet }}</div>
            <div class="ov-sub">of {{ $categories->count() }} categories have a limit</div>
        </div>

    </div>

    {{-- Monthly budget card --}}
    <div class="monthly-card">
        <div class="monthly-title">📅 Overall Monthly Budget</div>
        <div class="monthly-sub">A total spending cap for {{ now()->format('F Y') }}</div>

        @if($hasMonthly)
        <div class="monthly-amounts">
            <div>
                <div class="monthly-spent-label">Spent</div>
                <div class="monthly-spent-val">${{ number_format($totalSpentThisMonth, 2) }}</div>
            </div>
            <div>
                <div class="monthly-limit-label">Limit</div>
                <div class="monthly-limit-val">${{ number_format($monthlyLimit, 2) }}</div>
            </div>
        </div>
        <div class="monthly-bar-wrap">
            <div class="monthly-bar-fill {{ $monthlyPct >= 100 ? 'over' : '' }}"
                 style="width:{{ $monthlyPct }}%;"></div>
        </div>
        <div class="monthly-remaining {{ $isOver ? 'over' : '' }}">
            @if(!$isOver)
                <strong>${{ number_format($remaining, 2) }} remaining</strong> — {{ 100 - $monthlyPct }}% of budget left
            @else
                <strong>${{ number_format(abs($remaining), 2) }} over budget</strong> — consider adjusting your limit
            @endif
        </div>
        @else
        <div style="font-size:13px;color:#555;margin-bottom:4px;position:relative;z-index:1;">
            No monthly limit set yet. Set one to track your total spending this month.
        </div>
        @endif

        <form method="POST" action="{{ route('budgets.storeMonthly') }}" class="set-form">
            @csrf
            <input type="number" name="monthly_limit" class="set-input"
                   placeholder="{{ $hasMonthly ? 'Update limit, e.g. 3000' : 'Set limit, e.g. 3000' }}"
                   step="0.01" min="1"
                   value="{{ old('monthly_limit', $hasMonthly ? $monthlyLimit : '') }}">
            <button type="submit" class="set-btn">{{ $hasMonthly ? 'Update' : 'Set Budget' }}</button>
        </form>
        @error('monthly_limit')
            <div style="color:#f87171;font-size:12px;margin-top:8px;position:relative;z-index:1;">{{ $message }}</div>
        @enderror
    </div>

    {{-- Section grid --}}
    <div class="section-grid">

        {{-- Category list --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/>
                    </svg>
                    Per-Category Limits
                </div>
                <span style="font-size:12px;color:#bbb;">{{ $catBudgetsSet }} set</span>
            </div>

            @forelse($categories as $cat)
            @php
                $budget    = $budgets->get($cat->id);
                $spent     = (float) $monthlySpending->get($cat->id, 0);
                $limit     = $budget ? (float) $budget->monthly_limit : 0;
                $pct       = $limit > 0 ? min(100, round(($spent / $limit) * 100)) : 0;
                $fillColor = $pct >= 90 ? '#ef4444' : ($cat->color ?? '#FBCF97');
            @endphp
            <div>
                <div class="cat-row">
                    <div class="cat-dot" style="background:{{ $cat->color ?? '#FBCF97' }};"></div>

                    <div class="cat-info">
                        <div class="cat-name">{{ $cat->name }}</div>
                        <div class="cat-spent">
                            ${{ number_format($spent, 2) }} spent
                            @if($budget) · limit ${{ number_format($limit, 2) }} @endif
                        </div>
                    </div>

                    @if($budget)
                    <div class="cat-progress">
                        <div class="cat-track">
                            <div class="cat-fill" style="width:{{ $pct }}%;background:{{ $fillColor }};"></div>
                        </div>
                        <div class="cat-pct">{{ $pct }}%</div>
                    </div>
                    @endif

                    <div class="cat-limit">
                        @if($budget)
                            ${{ number_format($limit, 2) }}
                        @else
                            <span class="no-limit-badge">No limit</span>
                        @endif
                    </div>

                    <div class="cat-btns">
                        <button type="button" class="btn-edit" onclick="toggleEdit({{ $cat->id }})">
                            {{ $budget ? 'Edit' : 'Set' }}
                        </button>
                        @if($budget)
                        <form method="POST" action="{{ route('budgets.destroy', $budget->id) }}"
                              onsubmit="return confirm('Remove budget for {{ $cat->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-del">✕</button>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="inline-edit" id="edit-{{ $cat->id }}">
                    <form method="POST" action="{{ route('budgets.storeCategory') }}"
                          style="display:flex;gap:10px;align-items:center;width:100%;">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $cat->id }}">
                        <input type="number" name="monthly_limit" step="0.01" min="1"
                               placeholder="Limit, e.g. 500"
                               value="{{ $budget?->monthly_limit }}" required>
                        <button type="submit" class="save-btn">Save</button>
                        <button type="button" class="cancel-btn" onclick="toggleEdit({{ $cat->id }})">Cancel</button>
                    </form>
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#bbb;text-align:center;padding:24px 0;">No categories found.</p>
            @endforelse
        </div>

        {{-- Add / update panel --}}
        <div class="add-panel">
            <div class="panel">
                <div class="panel-title" style="margin-bottom:20px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add / Update Budget
                </div>

                <form method="POST" action="{{ route('budgets.storeCategory') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Choose a category…</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}{{ $budgets->has($cat->id) ? ' ✓' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Monthly Limit ($)</label>
                        <input type="number" name="monthly_limit" class="form-input"
                               placeholder="e.g. 500" step="0.01" min="1"
                               value="{{ old('monthly_limit') }}" required>
                        @error('monthly_limit') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn-add">
                        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Save Budget
                    </button>
                </form>

                <div class="tip-box">
                    <span>💡</span>
                    <span>Categories marked <strong>✓</strong> already have a budget. Saving again will update the existing limit.</span>
                </div>
            </div>
        </div>

    </div>

</main>
@endsection

@push('scripts')
    @vite('resources/js/budgets.js')
@endpush