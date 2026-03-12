@extends('layouts.app')

@section('title', 'Transactions')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/transactions.css'])
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
                <div class="cat-select-wrap" id="filter-cat-wrap">
                    <div class="cat-select-trigger" id="filter-cat-trigger" onclick="toggleCatDropdown('filter')">
                        <span class="cat-select-dot" id="filter-cat-dot"
                            style="background:{{ collect($categories)->firstWhere('id', request('category_id'))?->color ?? 'transparent' }};
                                   border:{{ request('category_id') ? 'none' : '1.5px dashed #ccc' }};"></span>
                        <span id="filter-cat-label">{{ collect($categories)->firstWhere('id', request('category_id'))?->name ?? 'All categories' }}</span>
                        <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:#aaa;stroke-width:2.5;stroke-linecap:round;margin-left:auto;"><polyline points="6,9 12,15 18,9"/></svg>
                    </div>
                    <div class="cat-select-dropdown hidden" id="filter-cat-dropdown">
                        <div class="cat-option" onclick="selectCat('filter', '', 'All categories', 'transparent', true)">
                            <span class="cat-option-dot" style="border:1.5px dashed #ccc;background:transparent;"></span>
                            All categories
                        </div>
                        @foreach($categories as $cat)
                            <div class="cat-option" onclick="selectCat('filter', '{{ $cat->id }}', '{{ $cat->name }}', '{{ $cat->color ?? '#FBCF97' }}', false)">
                                <span class="cat-option-dot" style="background:{{ $cat->color ?? '#FBCF97' }};"></span>
                                {{ $cat->name }}
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="category_id" id="filter-cat-input" value="{{ request('category_id') }}">
                </div>
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
                        @if($tx->category)
                            <div style="display:inline-flex;align-items:center;gap:7px;">
                                <span style="display:inline-block;width:9px;height:9px;border-radius:50%;flex-shrink:0;background:{{ $tx->category->color ?? '#FBCF97' }};"></span>
                                <span style="font-size:13px;color:#555;">{{ $tx->category->name }}</span>
                            </div>
                        @else
                            <span style="color:#ddd;">—</span>
                        @endif
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
                <div class="cat-select-wrap" id="modal-cat-wrap">
                    <div class="cat-select-trigger" id="modal-cat-trigger" onclick="toggleCatDropdown('modal')">
                        <span class="cat-select-dot" id="modal-cat-dot"
                            style="background:{{ collect($categories)->firstWhere('id', old('category_id'))?->color ?? 'transparent' }};
                                   border:{{ old('category_id') ? 'none' : '1.5px dashed #ccc' }};"></span>
                        <span id="modal-cat-label">{{ collect($categories)->firstWhere('id', old('category_id'))?->name ?? 'Select a category' }}</span>
                        <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:#aaa;stroke-width:2.5;stroke-linecap:round;margin-left:auto;"><polyline points="6,9 12,15 18,9"/></svg>
                    </div>
                    <div class="cat-select-dropdown hidden" id="modal-cat-dropdown">
                        @foreach($categories as $cat)
                            <div class="cat-option" onclick="selectCat('modal', '{{ $cat->id }}', '{{ $cat->name }}', '{{ $cat->color ?? '#FBCF97' }}', false)">
                                <span class="cat-option-dot" style="background:{{ $cat->color ?? '#FBCF97' }};"></span>
                                {{ $cat->name }}
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="category_id" id="modal-cat-input" value="{{ old('category_id') }}" required>
                </div>
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
    @vite('resources/js/transactions.js')
@endpush

