@extends('layouts.app')

@section('title', 'Transactions')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');
    * { box-sizing: border-box; }
    body { font-family: 'DM Sans', sans-serif; background: #F5F3EE; color: #1a1a1a; }
    h1,h2,h3,.font-display { font-family: 'Syne', sans-serif; }

    /* reuse sidebar styles from dashboard — this file only adds page-specific styles */

    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; }
    .page-title  { font-size:28px; font-weight:800; letter-spacing:-0.5px; }
    .page-sub    { font-size:14px; color:#888; margin-top:2px; }

    /* Filter bar */
    .filter-bar {
        background:#fff;
        border:1px solid #ede9e1;
        border-radius:18px;
        padding:18px 24px;
        display:flex;
        flex-wrap:wrap;
        gap:12px;
        align-items:flex-end;
        margin-bottom:22px;
    }

    .filter-group { display:flex; flex-direction:column; gap:4px; }
    .filter-label { font-size:11px; font-weight:600; color:#aaa; text-transform:uppercase; letter-spacing:0.5px; }

    .filter-input, .filter-select {
        padding:9px 14px;
        border-radius:12px;
        border:1.5px solid #ede9e1;
        font-size:13px;
        color:#1a1a1a;
        background:#fff;
        outline:none;
        transition:border-color 0.2s;
        height:40px;
    }
    .filter-input:focus, .filter-select:focus { border-color:#FBCF97; }

    .filter-btn {
        padding:9px 20px;
        border-radius:12px;
        border:none;
        font-size:13px;
        font-weight:700;
        cursor:pointer;
        height:40px;
        transition:all 0.2s;
    }
    .filter-btn.primary { background:#FBCF97; color:#1C1C1E; }
    .filter-btn.primary:hover { background:#f7bc71; }
    .filter-btn.ghost   { background:#f4f1eb; color:#666; }
    .filter-btn.ghost:hover { background:#e8e4dc; }

    /* Summary strip */
    .summary-strip {
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:14px;
        margin-bottom:20px;
    }

    .summary-card {
        background:#fff;
        border:1px solid #ede9e1;
        border-radius:16px;
        padding:18px 22px;
        display:flex;
        align-items:center;
        gap:14px;
    }

    .summary-icon {
        width:42px; height:42px;
        border-radius:13px;
        display:flex; align-items:center; justify-content:center;
        font-size:18px;
        flex-shrink:0;
    }

    .summary-label { font-size:12px; color:#aaa; font-weight:500; text-transform:uppercase; letter-spacing:0.4px; }
    .summary-val   { font-family:'Syne',sans-serif; font-size:20px; font-weight:800; letter-spacing:-0.5px; }

    /* Table panel */
    .table-panel {
        background:#fff;
        border:1px solid #ede9e1;
        border-radius:20px;
        overflow:hidden;
    }

    .table-panel table { width:100%; border-collapse:collapse; }

    .table-panel thead th {
        padding:14px 20px;
        text-align:left;
        font-size:11px;
        font-weight:700;
        color:#aaa;
        text-transform:uppercase;
        letter-spacing:0.6px;
        background:#fafaf8;
        border-bottom:1px solid #f0ece4;
    }

    .table-panel tbody tr {
        border-bottom:1px solid #f7f4ef;
        transition:background 0.15s;
    }
    .table-panel tbody tr:last-child { border-bottom:none; }
    .table-panel tbody tr:hover { background:#fdfcfa; }

    .table-panel tbody td { padding:14px 20px; font-size:13px; vertical-align:middle; }

    .tx-icon-cell {
        width:40px; height:40px;
        border-radius:13px;
        display:flex; align-items:center; justify-content:center;
        font-size:17px;
    }

    .badge {
        display:inline-flex;
        align-items:center;
        padding:3px 10px;
        border-radius:99px;
        font-size:11px;
        font-weight:700;
        letter-spacing:0.3px;
    }
    .badge.income  { background:#d1fae5; color:#065f46; }
    .badge.expense { background:#fff3e0; color:#9a5a10; }

    .amount-cell { font-family:'Syne',sans-serif; font-weight:700; font-size:14px; }
    .amount-cell.income  { color:#2EB872; }
    .amount-cell.expense { color:#1a1a1a; }

    .action-btn {
        padding:6px 12px;
        border-radius:9px;
        border:1.5px solid #e8e4dc;
        background:#fff;
        font-size:12px;
        font-weight:600;
        cursor:pointer;
        text-decoration:none;
        color:#555;
        display:inline-flex;
        align-items:center;
        gap:4px;
        transition:all 0.15s;
    }
    .action-btn:hover { border-color:#FBCF97; color:#9a5a10; background:#fff9f0; }
    .action-btn.danger:hover { border-color:#fca5a5; color:#ef4444; background:#fef2f2; }

    /* Pagination */
    .pagination-wrap {
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:16px 24px;
        border-top:1px solid #f0ece4;
        font-size:13px;
        color:#888;
    }

    /* Add btn */
    .add-btn {
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#FBCF97;
        border:none;
        border-radius:14px;
        padding:11px 20px;
        font-family:'Syne',sans-serif;
        font-size:14px;
        font-weight:700;
        color:#1C1C1E;
        cursor:pointer;
        text-decoration:none;
        transition:all 0.2s;
    }
    .add-btn:hover { background:#f7bc71; transform:translateY(-1px); }

    /* Flash */
    .flash {
        padding:14px 20px;
        border-radius:14px;
        margin-bottom:20px;
        font-size:13px;
        font-weight:600;
        display:flex;
        align-items:center;
        gap:10px;
    }
    .flash.success { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
    .flash.error   { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }

    /* Modal */
    .modal-overlay {
        position:fixed; inset:0;
        background:rgba(0,0,0,0.45);
        z-index:200;
        display:flex; align-items:center; justify-content:center;
        backdrop-filter:blur(4px);
    }
    .modal-box {
        background:#fff;
        border-radius:24px;
        padding:32px;
        width:100%;
        max-width:440px;
        position:relative;
        animation:fadeUp 0.25s ease;
    }
    @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }

    .modal-close {
        position:absolute; top:20px; right:20px;
        background:none; border:none;
        font-size:20px; color:#aaa; cursor:pointer;
    }
    .modal-title { font-family:'Syne',sans-serif; font-size:20px; font-weight:800; margin-bottom:4px; }
    .modal-sub   { font-size:13px; color:#aaa; margin-bottom:24px; }

    .form-group { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
    .form-label { font-size:12px; font-weight:600; color:#666; }
    .form-input, .form-select {
        padding:12px 16px;
        border-radius:13px;
        border:1.5px solid #ede9e1;
        font-size:14px;
        outline:none;
        transition:border-color 0.2s;
        width:100%;
        background:#fff;
        color:#1a1a1a;
    }
    .form-input:focus, .form-select:focus { border-color:#FBCF97; }
    .form-error { font-size:11px; color:#ef4444; }

    .type-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
    .type-opt  { text-align:center; padding:11px; border-radius:13px; border:2px solid #e5e7eb; background:#fff; font-size:13px; font-weight:700; color:#aaa; cursor:pointer; transition:all 0.15s; user-select:none; }
    .type-opt.active-expense { border-color:#FBCF97; background:#fff9f0; color:#9a5a10; }
    .type-opt.active-income  { border-color:#2EB872; background:#f0fdf4; color:#065f46; }

    .submit-btn {
        width:100%;
        background:#FBCF97;
        border:none;
        border-radius:13px;
        padding:13px;
        font-family:'Syne',sans-serif;
        font-size:14px;
        font-weight:700;
        color:#1C1C1E;
        cursor:pointer;
        margin-top:8px;
        transition:all 0.2s;
        display:flex; align-items:center; justify-content:center; gap:8px;
    }
    .submit-btn:hover { background:#f7bc71; }

    @media(max-width:900px) {
        .summary-strip { grid-template-columns:1fr; }
        .filter-bar    { flex-direction:column; align-items:stretch; }
    }
</style>
@endpush

@section('content')

<main class="main-content">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flash success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <div class="page-header">
        <div>
            <div class="page-title">Transactions</div>
            <div class="page-sub">{{ $transactions->total() }} records found</div>
        </div>
        <button class="add-btn" onclick="openModal('add-modal')">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Transaction
        </button>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('transactions.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <span class="filter-label">Search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Description…" class="filter-input" style="width:180px;">
            </div>
            <div class="filter-group">
                <span class="filter-label">Type</span>
                <select name="type" class="filter-select">
                    <option value="">All</option>
                    <option value="Income"  {{ request('type') === 'Income'  ? 'selected' : '' }}>Income</option>
                    <option value="Expense" {{ request('type') === 'Expense' ? 'selected' : '' }}>Expense</option>
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Category</span>
                <select name="category_id" class="filter-select">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">From</span>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
            </div>
            <div class="filter-group">
                <span class="filter-label">To</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input">
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end;">
                <button type="submit" class="filter-btn primary">Filter</button>
                <a href="{{ route('transactions.index') }}" class="filter-btn ghost" style="display:inline-flex;align-items:center;text-decoration:none;">Clear</a>
            </div>
        </div>
    </form>

    {{-- Summary strip --}}
    @php
        $allTx     = \App\Models\Transaction::where('user_id', Auth::id())->thisMonth()->get();
        $income    = $allTx->where('type','Income')->sum('amount');
        $expenses  = $allTx->where('type','Expense')->sum('amount');
        $net       = $income - $expenses;
    @endphp
    <div class="summary-strip">
        <div class="summary-card">
            <div class="summary-icon" style="background:#d1fae5;">💰</div>
            <div>
                <div class="summary-label">Income this month</div>
                <div class="summary-val" style="color:#2EB872;">${{ number_format($income, 2) }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon" style="background:#fff3e0;">💸</div>
            <div>
                <div class="summary-label">Expenses this month</div>
                <div class="summary-val">${{ number_format($expenses, 2) }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon" style="background:{{ $net >= 0 ? '#d1fae5' : '#fee2e2' }};">{{ $net >= 0 ? '📈' : '📉' }}</div>
            <div>
                <div class="summary-label">Net balance</div>
                <div class="summary-val" style="color:{{ $net >= 0 ? '#2EB872' : '#ef4444' }};">
                    {{ $net >= 0 ? '+' : '' }}${{ number_format(abs($net), 2) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-panel">
        <table>
            <thead>
                <tr>
                    <th>Transaction</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Receipt</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div class="tx-icon-cell" style="background:{{ $tx->type === 'Income' ? '#d1fae5' : '#fff3e0' }};">
                                {{ $tx->type === 'Income' ? '💰' : '💸' }}
                            </div>
                            <div>
                                <div style="font-weight:600;color:#1a1a1a;">{{ $tx->description }}</div>
                                <div style="font-size:11px;color:#bbb;">ID #{{ $tx->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:13px;color:#555;">{{ $tx->category->name ?? '—' }}</span>
                    </td>
                    <td>
                        <span class="badge {{ strtolower($tx->type) }}">{{ $tx->type }}</span>
                    </td>
                    <td style="color:#888;">{{ $tx->date->format('M d, Y') }}</td>
                    <td>
                        <span class="amount-cell {{ strtolower($tx->type) }}">{{ $tx->formatted_amount }}</span>
                    </td>
                    <td>
                        @if($tx->receipt_image_path)
                            <a href="{{ $tx->receipt_url }}" target="_blank" class="action-btn" style="font-size:11px;">🧾 View</a>
                        @else
                            <span style="color:#ddd;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('transactions.edit', $tx) }}" class="action-btn">✏️ Edit</a>
                            <form method="POST" action="{{ route('transactions.destroy', $tx) }}"
                                  onsubmit="return confirm('Delete this transaction?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn danger">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:#ccc;">
                        <div style="font-size:32px;margin-bottom:8px;">📭</div>
                        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:15px;color:#bbb;">No transactions yet</div>
                        <div style="font-size:13px;color:#ddd;margin-top:4px;">Add your first transaction using the button above.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div class="pagination-wrap">
            <span>Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}</span>
            <div style="display:flex;gap:6px;">
                @if($transactions->onFirstPage())
                    <span class="action-btn" style="opacity:0.4;cursor:default;">← Prev</span>
                @else
                    <a href="{{ $transactions->previousPageUrl() }}" class="action-btn">← Prev</a>
                @endif
                @if($transactions->hasMorePages())
                    <a href="{{ $transactions->nextPageUrl() }}" class="action-btn">Next →</a>
                @else
                    <span class="action-btn" style="opacity:0.4;cursor:default;">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>

</main>

{{-- ══════════ ADD MODAL ══════════ --}}
<div id="add-modal" class="modal-overlay hidden" onclick="if(event.target===this)closeModal('add-modal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal('add-modal')">✕</button>
        <div class="modal-title">Add Transaction</div>
        <div class="modal-sub">Log a new income or expense</div>

        <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Type --}}
            <div class="form-group">
                <span class="form-label">Type</span>
                <div class="type-grid">
                    <div class="type-opt active-expense" id="opt-expense" onclick="setType('Expense')">💸 Expense</div>
                    <div class="type-opt" id="opt-income"  onclick="setType('Income')">💰 Income</div>
                </div>
                <input type="hidden" name="type" id="type-input" value="Expense">
                @error('type') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Amount --}}
            <div class="form-group">
                <label class="form-label">Amount ($)</label>
                <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00"
                       value="{{ old('amount') }}" class="form-input" required>
                @error('amount') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="description" placeholder="e.g. Grocery run"
                       value="{{ old('description') }}" class="form-input" required>
                @error('description') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Category --}}
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Date --}}
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-input" required>
                @error('date') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Receipt --}}
            <div class="form-group">
                <label class="form-label">Receipt Image (optional)</label>
                <input type="file" name="receipt_image" accept="image/*" class="form-input" style="padding:8px 14px;">
                @error('receipt_image') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="submit-btn">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
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

    function setType(val) {
        document.getElementById('type-input').value = val;
        const expOpt = document.getElementById('opt-expense');
        const incOpt = document.getElementById('opt-income');
        expOpt.className = 'type-opt' + (val === 'Expense' ? ' active-expense' : '');
        incOpt.className = 'type-opt' + (val === 'Income'  ? ' active-income'  : '');
    }

    // Re-open modal with errors if validation failed
    @if($errors->any())
        openModal('add-modal');
    @endif
</script>
@endpush