@extends('layouts.app')

@section('title', 'Budgets')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap');

    *, *::before, *::after { box-sizing: border-box; }

    body { font-family: 'DM Sans', sans-serif; background: #F5F3EE; color: #1a1a1a; }
    h1, h2, h3, .font-display { font-family: 'Syne', sans-serif; }

    .main-content { margin-left: 255px; padding: 40px 48px; min-height: 100vh; }

    /* ── Flash ── */
    .flash {
        display: flex; align-items: center; gap: 10px;
        background: #d1fae5; border: 1px solid #6ee7b7;
        border-radius: 14px; padding: 14px 20px;
        font-size: 13px; font-weight: 600; color: #065f46;
        margin-bottom: 28px; animation: fadeUp 0.35s ease both;
    }

    /* ── Page header ── */
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 32px; animation: fadeUp 0.35s ease both;
    }
    .page-title { font-size: 28px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
    .page-sub   { font-size: 14px; color: #999; margin-top: 3px; }

    /* ── Overview cards ── */
    .overview-grid {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 18px; margin-bottom: 28px;
    }
    .ov-card {
        background: #fff; border-radius: 20px; border: 1px solid #ede9e1;
        padding: 24px; position: relative; overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        animation: fadeUp 0.4s ease both;
    }
    .ov-card:nth-child(1) { animation-delay: 0.05s; }
    .ov-card:nth-child(2) { animation-delay: 0.10s; }
    .ov-card:nth-child(3) { animation-delay: 0.15s; }
    .ov-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.07); }
    .ov-card.dark  { background: #1C1C1E; border-color: #1C1C1E; }
    .ov-card.green { background: #2EB872; border-color: #2EB872; }
    .ov-card.red   { background: #fee2e2; border-color: #fecaca; }

    .ov-label { font-size: 11px; color: #bbb; font-weight: 500; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px; }
    .ov-card.dark  .ov-label { color: #555; }
    .ov-card.green .ov-label { color: rgba(255,255,255,0.6); }
    .ov-card.red   .ov-label { color: #ef4444; }

    .ov-value { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; color: #1a1a1a; letter-spacing: -1px; }
    .ov-card.dark  .ov-value { color: #FBCF97; }
    .ov-card.green .ov-value { color: #fff; }
    .ov-card.red   .ov-value { color: #dc2626; }

    .ov-sub { font-size: 12px; color: #bbb; margin-top: 4px; }
    .ov-card.dark  .ov-sub { color: #444; }
    .ov-card.green .ov-sub { color: rgba(255,255,255,0.55); }
    .ov-card.red   .ov-sub { color: #f87171; }

    .ov-ring-wrap { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); }
    .ov-ring { transform: rotate(-90deg); }
    .ov-ring-bg   { fill: none; stroke: #f0ece4; stroke-width: 5; }
    .ov-ring-fill { fill: none; stroke-width: 5; stroke-linecap: round; }
    .ov-ring-pct  {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif; font-size: 11px; font-weight: 800;
    }

    /* ── Monthly card ── */
    .monthly-card {
        background: #1C1C1E; border-radius: 20px; padding: 28px;
        margin-bottom: 22px; position: relative; overflow: hidden;
        animation: fadeUp 0.4s ease 0.18s both;
    }
    .monthly-card::before {
        content: ''; position: absolute; top: -50px; right: -50px;
        width: 180px; height: 180px; border-radius: 50%;
        background: rgba(251,207,151,0.07);
    }
    .monthly-card::after {
        content: ''; position: absolute; bottom: -40px; left: 30px;
        width: 120px; height: 120px; border-radius: 50%;
        background: rgba(46,184,114,0.06);
    }

    .monthly-title { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 4px; }
    .monthly-sub   { font-size: 12px; color: #555; margin-bottom: 22px; }

    .monthly-amounts {
        display: flex; justify-content: space-between; align-items: flex-end;
        margin-bottom: 14px; position: relative; z-index: 1;
    }
    .monthly-spent-label { font-size: 11px; color: #555; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
    .monthly-spent-val   { font-family: 'Syne', sans-serif; font-size: 30px; font-weight: 800; color: #FBCF97; }
    .monthly-limit-label { font-size: 11px; color: #555; text-align: right; margin-bottom: 3px; }
    .monthly-limit-val   { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; color: #888; text-align: right; }

    .monthly-bar-wrap { height: 7px; background: #2a2a2c; border-radius: 99px; overflow: hidden; margin-bottom: 10px; position: relative; z-index: 1; }
    .monthly-bar-fill { height: 100%; border-radius: 99px; transition: width 0.9s ease; background: linear-gradient(90deg, #2EB872, #FBCF97); }
    .monthly-bar-fill.over { background: linear-gradient(90deg, #f87171, #ef4444); }

    .monthly-remaining { font-size: 13px; color: #555; position: relative; z-index: 1; }
    .monthly-remaining strong { color: #2EB872; }
    .monthly-remaining.over strong { color: #f87171; }

    .set-form { display: flex; gap: 10px; margin-top: 18px; position: relative; z-index: 1; }
    .set-input {
        flex: 1; padding: 12px 16px; background: #2a2a2c; border: 1.5px solid #333;
        border-radius: 14px; font-family: 'DM Sans', sans-serif;
        font-size: 14px; font-weight: 600; color: #fff; outline: none;
        transition: border-color 0.2s;
    }
    .set-input::placeholder { color: #555; }
    .set-input:focus { border-color: #FBCF97; }
    .set-btn {
        background: #FBCF97; border: none; border-radius: 14px;
        padding: 12px 20px; font-family: 'Syne', sans-serif;
        font-size: 13px; font-weight: 700; color: #1C1C1E;
        cursor: pointer; white-space: nowrap; transition: all 0.2s;
    }
    .set-btn:hover { background: #f7bc71; transform: translateY(-1px); }

    /* ── Section grid ── */
    .section-grid {
        display: grid; grid-template-columns: 1fr 360px;
        gap: 22px; animation: fadeUp 0.4s ease 0.2s both;
    }

    /* ── Panel ── */
    .panel { background: #fff; border-radius: 20px; border: 1px solid #ede9e1; padding: 28px; }
    .panel-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
    .panel-title {
        font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; color: #1a1a1a;
        display: flex; align-items: center; gap: 8px;
    }

    /* ── Category rows ── */
    .cat-row {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 0; border-bottom: 1px solid #f4f1eb;
    }
    .cat-row:last-child { border-bottom: none; padding-bottom: 0; }

    .cat-dot { width: 11px; height: 11px; border-radius: 50%; flex-shrink: 0; }

    .cat-info { flex: 1; min-width: 0; }
    .cat-name  { font-size: 13px; font-weight: 600; color: #1a1a1a; }
    .cat-spent { font-size: 11px; color: #bbb; margin-top: 2px; }

    .cat-progress { width: 90px; }
    .cat-track { height: 5px; background: #f0ece4; border-radius: 99px; overflow: hidden; margin-bottom: 3px; }
    .cat-fill  { height: 100%; border-radius: 99px; transition: width 0.7s ease; }
    .cat-pct   { font-size: 10px; color: #bbb; text-align: right; }

    .cat-limit { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; color: #1a1a1a; white-space: nowrap; min-width: 72px; text-align: right; }

    .cat-btns { display: flex; gap: 6px; flex-shrink: 0; }

    .btn-edit {
        background: #f5f3ee; border: 1px solid #ede9e1; border-radius: 10px;
        padding: 6px 12px; font-size: 11px; font-weight: 600; color: #666;
        cursor: pointer; transition: all 0.15s; white-space: nowrap;
    }
    .btn-edit:hover { background: #FBCF97; border-color: #FBCF97; color: #1C1C1E; }

    .btn-del {
        background: none; border: 1px solid #fecaca; border-radius: 10px;
        padding: 6px 10px; font-size: 11px; color: #ef4444;
        cursor: pointer; transition: all 0.15s;
    }
    .btn-del:hover { background: #fee2e2; }

    .no-limit-badge {
        background: #fef3c7; border: 1px solid #fde68a;
        border-radius: 8px; padding: 3px 10px;
        font-size: 11px; font-weight: 600; color: #92400e; white-space: nowrap;
    }

    /* ── Inline edit ── */
    .inline-edit {
        display: none; background: #fafaf8; border: 1.5px solid #ede9e1;
        border-radius: 14px; padding: 12px 14px; margin-top: 8px;
        align-items: center; gap: 10px;
    }
    .inline-edit.open { display: flex; }
    .inline-edit input {
        flex: 1; padding: 9px 14px; border-radius: 10px;
        border: 1.5px solid #ede9e1; font-size: 14px; font-weight: 600;
        outline: none; background: #fff; transition: border-color 0.2s;
    }
    .inline-edit input:focus { border-color: #FBCF97; }
    .inline-edit .save-btn {
        background: #FBCF97; border: none; border-radius: 10px;
        padding: 9px 16px; font-family: 'Syne', sans-serif;
        font-size: 12px; font-weight: 700; color: #1C1C1E; cursor: pointer;
    }
    .inline-edit .cancel-btn {
        background: none; border: 1px solid #e5e7eb; border-radius: 10px;
        padding: 9px 12px; font-size: 12px; color: #aaa; cursor: pointer;
    }

    /* ── Add form panel ── */
    .add-panel { position: sticky; top: 24px; }

    .form-group { margin-bottom: 16px; }
    .form-label {
        display: block; font-size: 11px; font-weight: 600; color: #999;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 7px;
    }
    .form-input, .form-select {
        width: 100%; padding: 13px 16px; border-radius: 14px;
        border: 1.5px solid #ede9e1; font-family: 'DM Sans', sans-serif;
        font-size: 14px; color: #1a1a1a; background: #fafaf8; outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus, .form-select:focus {
        border-color: #FBCF97; box-shadow: 0 0 0 3px rgba(251,207,151,0.2); background: #fff;
    }
    .form-error { font-size: 12px; color: #ef4444; margin-top: 5px; }

    .btn-add {
        width: 100%; background: #FBCF97; border: none; border-radius: 14px;
        padding: 14px; font-family: 'Syne', sans-serif; font-size: 14px;
        font-weight: 700; color: #1C1C1E; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: all 0.2s;
    }
    .btn-add:hover { background: #f7bc71; transform: translateY(-1px); }
    .btn-add svg { width: 15px; height: 15px; stroke: #1C1C1E; fill: none; stroke-width: 2.5; stroke-linecap: round; }

    .tip-box {
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px;
        padding: 14px 16px; font-size: 13px; color: #065f46;
        margin-top: 16px; display: flex; gap: 10px; align-items: flex-start;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1200px) {
        .overview-grid { grid-template-columns: repeat(2, 1fr); }
        .section-grid  { grid-template-columns: 1fr; }
        .add-panel     { position: static; }
    }
    @media (max-width: 768px) {
        .main-content { margin-left: 0; padding: 24px 20px; }
        .overview-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<main class="main-content">

    @if(session('success'))
    <div class="flash">✅ {{ session('success') }}</div>
    @endif

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <div class="page-title">Budgets</div>
            <div class="page-sub">Set limits for the whole month and per category — {{ now()->format('F Y') }}</div>
        </div>
    </div>

    {{-- Overview cards --}}
    @php
        $monthlyPct    = $hasMonthly && $monthlyLimit > 0
                         ? min(100, round(($totalSpentThisMonth / $monthlyLimit) * 100))
                         : 0;
        $remaining     = $hasMonthly ? $monthlyLimit - $totalSpentThisMonth : null;
        $catBudgetsSet = $budgets->count();
        $isOver        = $hasMonthly && $remaining < 0;
    @endphp

    <div class="overview-grid">

        {{-- Spent this month --}}
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
                    <circle class="ov-ring-bg"   cx="36" cy="36" r="{{ $r }}"/>
                    <circle class="ov-ring-fill"  cx="36" cy="36" r="{{ $r }}"
                        stroke="{{ $monthlyPct >= 90 ? '#f87171' : '#FBCF97' }}"
                        stroke-dasharray="{{ $circ }}"
                        stroke-dashoffset="{{ $offset }}"/>
                </svg>
                <div class="ov-ring-pct" style="color:#FBCF97;">{{ $monthlyPct }}%</div>
            </div>
            @endif
        </div>

        {{-- Remaining / over --}}
        <div class="ov-card {{ $isOver ? 'red' : ($hasMonthly ? 'green' : '') }}">
            <div class="ov-label">
                @if(!$hasMonthly) Monthly Limit
                @elseif($isOver)  Over Budget
                @else             Remaining
                @endif
            </div>
            @if($hasMonthly)
                <div class="ov-value">${{ number_format(abs($remaining), 2) }}</div>
                <div class="ov-sub">{{ $isOver ? 'over your monthly limit' : 'left for the month' }}</div>
            @else
                <div class="ov-value">—</div>
                <div class="ov-sub">Set a monthly limit below</div>
            @endif
        </div>

        {{-- Category budgets set --}}
        <div class="ov-card">
            <div class="ov-label">Category Budgets</div>
            <div class="ov-value">{{ $catBudgetsSet }}</div>
            <div class="ov-sub">of {{ $categories->count() }} categories have a limit</div>
        </div>

    </div>

    {{-- Overall monthly budget card --}}
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

    {{-- Section grid: category list + add form --}}
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
                $budget   = $budgets->get($cat->id);
                $spent    = (float) $monthlySpending->get($cat->id, 0);
                $limit    = $budget ? (float) $budget->monthly_limit : 0;
                $pct      = $limit > 0 ? min(100, round(($spent / $limit) * 100)) : 0;
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

                {{-- Inline edit --}}
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
<script>
    function toggleEdit(catId) {
        const el = document.getElementById('edit-' + catId);
        el.classList.toggle('open');
        if (el.classList.contains('open')) {
            el.querySelector('input[type="number"]').focus();
        }
    }
</script>
@endpush