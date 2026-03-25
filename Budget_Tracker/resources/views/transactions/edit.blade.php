@extends('layouts.app')

@section('title', 'Edit Transaction')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/transactions.css'])
@endpush

@section('content')
    <main class="main-content">

        @if (session('success'))
            <div class="flash success"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-width="2"
                        d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12s4.477 10 10 10ZM7 12l4 3l5-7" />
                </svg> {{ session('success') }}</div>
        @endif
        @if ($errors->any() && !$errors->has('name') && !$errors->has('color'))
            <div class="flash error"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M12.713 16.713Q13 16.425 13 16t-.288-.712T12 15t-.712.288T11 16t.288.713T12 17t.713-.288M11 13h2V7h-2zm1 9q-2.075 0-3.9-.788t-3.175-2.137T2.788 15.9T2 12t.788-3.9t2.137-3.175T8.1 2.788T12 2t3.9.788t3.175 2.137T21.213 8.1T22 12t-.788 3.9t-2.137 3.175t-3.175 2.138T12 22m0-2q3.35 0 5.675-2.325T20 12t-2.325-5.675T12 4T6.325 6.325T4 12t2.325 5.675T12 20m0-8" />
                </svg> Please fix the errors below.</div>
        @endif

        <div class="page-header">
            <div class="page-title">Edit Transaction</div>
            <a href="{{ route('transactions.index') }}" class="back-btn">← Back to Transactions</a>
        </div>

        <div class="edit-card">
            <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Type --}}
                <div class="form-group">
                    <span class="form-label">Type</span>
                    <div class="type-grid">
                        <div class="type-opt {{ $transaction->type === 'Expense' ? 'active-expense' : '' }}"
                            id="opt-expense" onclick="setType('Expense')">💸 Expense</div>
                        <div class="type-opt {{ $transaction->type === 'Income' ? 'active-income' : '' }}" id="opt-income"
                            onclick="setType('Income')">💰 Income</div>
                    </div>
                    <input type="hidden" name="type" id="type-input" value="{{ old('type', $transaction->type) }}">
                    @error('type')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Amount --}}
                <div class="form-group">
                    <label class="form-label">Amount ($)</label>
                    <input type="number" name="amount" step="0.01" min="0.01"
                        value="{{ old('amount', $transaction->amount) }}" class="form-input" required>
                    @error('amount')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" value="{{ old('description', $transaction->description) }}"
                        class="form-input" required>
                    @error('description')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select a category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Date --}}
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}"
                        class="form-input" required>
                    @error('date')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Receipt --}}
                <div class="form-group">
                    <label class="form-label">Receipt Image</label>

                    @if ($transaction->receipt_image_path)
                        <div class="receipt-preview">
                            <img src="{{ $transaction->receipt_url }}" alt="Receipt">
                            <div>
                                <a href="{{ $transaction->receipt_url }}" target="_blank">View current receipt</a>
                                <div style="font-size:11px;color:#bbb;margin-top:2px;">Upload a new file to replace it</div>
                            </div>
                        </div>
                        <label class="remove-label">
                            <input type="checkbox" name="remove_receipt" value="1"
                                {{ old('remove_receipt') ? 'checked' : '' }}>
                            Remove current receipt
                        </label>
                    @endif

                    <input type="file" name="receipt_image" accept="image/*" class="form-input"
                        style="padding:8px 14px;margin-top:8px;">
                    @error('receipt_image')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="btn-row">
                    <button type="submit" class="submit-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24"
                            style="width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round">
                            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                            <polyline points="17,21 17,13 7,13 7,21" />
                            <polyline points="7,3 7,8 15,8" />
                        </svg>
                    </button>
                    <button type="button" class="delete-btn" onclick="document.getElementById('delete-form').submit()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                        </svg>
                    </button>
                </div>
            </form>

            <form id="delete-form" method="POST" action="{{ route('transactions.destroy', $transaction) }}"
                onsubmit="return confirm('Permanently delete this transaction?')">
                @csrf @method('DELETE')
            </form>
        </div>

    </main>
@endsection

@push('scripts')
    @vite('resources/js/transactions.js')
@endpush
