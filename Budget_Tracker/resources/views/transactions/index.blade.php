@extends('layouts.app')

@section('title', 'Expenses')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/transactions.css'])
@endpush

@section('content')

    <main class="main-content">

        @if (session('success'))
            <div class="flash success">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-width="2"
                        d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12s4.477 10 10 10ZM7 12l4 3l5-7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="flash error">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M12.713 16.713Q13 16.425 13 16t-.288-.712T12 15t-.712.288T11 16t.288.713T12 17t.713-.288M11 13h2V7h-2zm1 9q-2.075 0-3.9-.788t-3.175-2.137T2.788 15.9T2 12t.788-3.9t2.137-3.175T8.1 2.788T12 2t3.9.788t3.175 2.137T21.213 8.1T22 12t-.788 3.9t-2.137 3.175t-3.175 2.138T12 22m0-2q3.35 0 5.675-2.325T20 12t-2.325-5.675T12 4T6.325 6.325T4 12t2.325 5.675T12 20m0-8" />
                </svg>
                Please fix the errors below.
            </div>
        @endif

        {{-- Reopen add modal automatically if validation failed --}}
        @if ($errors->any())
            <span data-reopen-modal="add-modal" style="display:none;"></span>
        @endif

        {{-- Header --}}
        <div class="page-header">
            <div>
                <div class="page-title">Expenses</div>
                <div class="page-sub">{{ $transactions->total() }} records found</div>
            </div>
            <button class="add-btn" data-open-modal="add-modal">
                <svg viewBox="0 0 24 24"
                    style="width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Add Expense
            </button>
        </div>

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('transactions.index') }}">
            <div class="filter-bar">
                <div class="filter-group">
                    <span class="filter-label">Search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Description…"
                        class="filter-input" style="width:180px;">
                </div>
                {{-- Type filter removed — all transactions are expenses --}}
                <div class="filter-group">
                    <span class="filter-label">Category</span>
                    <div class="cat-select-wrap" id="filter-cat-wrap">
                        <div class="cat-select-trigger" id="filter-cat-trigger" data-toggle-dropdown="filter-cat-dropdown">
                            <span class="cat-select-dot" id="filter-cat-dot"
                                style="background:{{ collect($categories)->firstWhere('id', request('category_id'))?->color ?? 'transparent' }};
                                   border:{{ request('category_id') ? 'none' : '1.5px dashed #ccc' }};"></span>
                            <span id="filter-cat-label">
                                {{ collect($categories)->firstWhere('id', request('category_id'))?->name ?? 'All categories' }}
                            </span>
                            <svg viewBox="0 0 24 24"
                                style="width:12px;height:12px;fill:none;stroke:#aaa;stroke-width:2.5;stroke-linecap:round;margin-left:auto;">
                                <polyline points="6,9 12,15 18,9" />
                            </svg>
                        </div>
                        <div class="cat-select-dropdown hidden" id="filter-cat-dropdown">
                            <div class="cat-option" data-cat-target="filter" data-cat-id="" data-cat-name="All categories"
                                data-cat-color="transparent" data-cat-dashed="true">
                                <span class="cat-option-dot"
                                    style="border:1.5px dashed #ccc;background:transparent;"></span>
                                All categories
                            </div>
                            @foreach ($categories as $cat)
                                <div class="cat-option" data-cat-target="filter" data-cat-id="{{ $cat->id }}"
                                    data-cat-name="{{ $cat->name }}" data-cat-color="{{ $cat->color ?? '#FBCF97' }}"
                                    data-cat-dashed="false">
                                    <span class="cat-option-dot" style="background:{{ $cat->color ?? '#FBCF97' }};"></span>
                                    {{ $cat->name }}
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="category_id" id="filter-cat-input"
                            value="{{ request('category_id') }}">
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
                    <a href="{{ route('transactions.index') }}" class="filter-btn ghost"
                        style="display:inline-flex;align-items:center;text-decoration:none;">Clear</a>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="table-panel">
            <table>
                <thead>
                    <tr>
                        <th>Expense</th>
                        <th>Category</th>
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
                                    <div class="tx-icon-cell" style="background:#fff3e0;">💸</div>
                                    <div>
                                        <div style="font-weight:600;color:#1a1a1a;">{{ $tx->description ?? '—' }}</div>
                                        <div style="font-size:11px;color:#bbb;">ID #{{ $tx->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($tx->category)
                                    <div style="display:inline-flex;align-items:center;gap:7px;">
                                        <span
                                            style="display:inline-block;width:9px;height:9px;border-radius:50%;flex-shrink:0;background:{{ $tx->category->color ?? '#FBCF97' }};"></span>
                                        <span style="font-size:13px;color:#555;">{{ $tx->category->name }}</span>
                                    </div>
                                @else
                                    <span style="color:#ddd;">—</span>
                                @endif
                            </td>
                            {{-- Type column removed --}}
                            <td style="color:#888;">{{ $tx->date->format('M d, Y') }}</td>
                            <td>
                                <span class="amount-cell debit">{{ $tx->formatted_amount }}</span>
                            </td>
                            <td>
                                @if ($tx->receipt_image_path)
                                    <a href="{{ $tx->receipt_url }}" target="_blank" class="action-btn"
                                        style="font-size:11px;">🧾 View</a>
                                @else
                                    <span style="color:#ddd;font-size:12px;">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <a href="{{ route('transactions.edit', $tx) }}" class="action-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 32 32">
                                            <path fill="currentColor"
                                                d="M27.307 6.107L30 3.414L28.586 2l-2.693 2.693L24.8 3.6a1.933 1.933 0 0 0-2.8 0l-18 18V28h6.4l18-18a1.933 1.933 0 0 0 0-2.8ZM9.6 26H6v-3.6L23.4 5L27 8.6ZM9 11.586L16.586 4L18 5.414L10.414 13z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('transactions.destroy', $tx) }}"
                                        onsubmit="return confirm('Delete this expense?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px;color:#ccc;">
                                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:15px;color:#bbb;">No
                                    expenses yet</div>
                                <div style="font-size:13px;color:#ddd;margin-top:4px;">Add your first expense using the
                                    button above.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($transactions->hasPages())
                <div class="pagination-wrap">
                    <span>Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of
                        {{ $transactions->total() }}</span>
                    <div style="display:flex;gap:6px;">
                        @if ($transactions->onFirstPage())
                            <span class="action-btn" style="opacity:0.4;cursor:default;">← Prev</span>
                        @else
                            <a href="{{ $transactions->previousPageUrl() }}" class="action-btn">← Prev</a>
                        @endif
                        @if ($transactions->hasMorePages())
                            <a href="{{ $transactions->nextPageUrl() }}" class="action-btn">Next →</a>
                        @else
                            <span class="action-btn" style="opacity:0.4;cursor:default;">Next →</span>
                        @endif
                    </div>
                </div>
            @endif
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
                {{-- inside the add-modal <form>, after @csrf --}}
                <input type="hidden" name="type" value="Expense">

                {{-- Type toggle removed --}}

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
                    <div class="cat-select-wrap" id="modal-cat-wrap">
                        <div class="cat-select-trigger" id="modal-cat-trigger" data-toggle-dropdown="modal-cat-dropdown">
                            <span class="cat-select-dot" id="modal-cat-dot"
                                style="background:{{ collect($categories)->firstWhere('id', old('category_id'))?->color ?? 'transparent' }};
                                   border:{{ old('category_id') ? 'none' : '1.5px dashed #ccc' }};"></span>
                            <span id="modal-cat-label">
                                {{ collect($categories)->firstWhere('id', old('category_id'))?->name ?? 'Select a category' }}
                            </span>
                            <svg viewBox="0 0 24 24"
                                style="width:12px;height:12px;fill:none;stroke:#aaa;stroke-width:2.5;stroke-linecap:round;margin-left:auto;">
                                <polyline points="6,9 12,15 18,9" />
                            </svg>
                        </div>
                        <div class="cat-select-dropdown hidden" id="modal-cat-dropdown">
                            @foreach ($categories as $cat)
                                <div class="cat-option" data-cat-target="modal" data-cat-id="{{ $cat->id }}"
                                    data-cat-name="{{ $cat->name }}" data-cat-color="{{ $cat->color ?? '#FBCF97' }}"
                                    data-cat-dashed="false">
                                    <span class="cat-option-dot"
                                        style="background:{{ $cat->color ?? '#FBCF97' }};"></span>
                                    {{ $cat->name }}
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="category_id" id="modal-cat-input" value="{{ old('category_id') }}">
                    </div>
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
                    <input type="file" name="receipt_image" id="receipt-scan-input" accept="image/*"
                        class="form-input" style="padding:8px 14px;">
                    <div id="ocr-status" class="text-sm mt-1 hidden" style="color: #6366f1;">
                        <span class="spinner"></span> Scanning receipt details...
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <svg viewBox="0 0 24 24"
                        style="width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round">
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
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    @vite('resources/js/transactions.js')
@endpush
