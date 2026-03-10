@extends('layouts.app')

@section('title', 'Edit Transaction')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');
    * { box-sizing: border-box; }
    body { font-family: 'DM Sans', sans-serif; background: #F5F3EE; color: #1a1a1a; }
    h1,h2,h3 { font-family: 'Syne', sans-serif; }

    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; }
    .page-title  { font-size:28px; font-weight:800; letter-spacing:-0.5px; }
    .back-btn    { display:inline-flex; align-items:center; gap:6px; color:#888; font-size:13px; font-weight:600; text-decoration:none; padding:8px 14px; border-radius:12px; border:1.5px solid #e8e4dc; transition:all 0.15s; }
    .back-btn:hover { border-color:#FBCF97; color:#9a5a10; background:#fff9f0; }

    .edit-card {
        background:#fff;
        border:1px solid #ede9e1;
        border-radius:22px;
        padding:36px;
        max-width:560px;
        margin: 0 auto;
    }

    .form-group { display:flex; flex-direction:column; gap:5px; margin-bottom:18px; }
    .form-label { font-size:12px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:0.5px; }
    .form-input, .form-select {
        padding:13px 16px;
        border-radius:13px;
        border:1.5px solid #ede9e1;
        font-size:14px;
        outline:none;
        transition:border-color 0.2s;
        width:100%;
        background:#fff;
        color:#1a1a1a;
    }
    .form-input:focus, .form-select:focus { border-color:#FBCF97; box-shadow:0 0 0 3px #fbcf9720; }
    .form-error { font-size:11px; color:#ef4444; margin-top:2px; }

    .type-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
    .type-opt  { text-align:center; padding:12px; border-radius:13px; border:2px solid #e5e7eb; background:#fff; font-size:13px; font-weight:700; color:#aaa; cursor:pointer; transition:all 0.15s; user-select:none; }
    .type-opt.active-expense { border-color:#FBCF97; background:#fff9f0; color:#9a5a10; }
    .type-opt.active-income  { border-color:#2EB872; background:#f0fdf4; color:#065f46; }

    .receipt-preview {
        display:flex; align-items:center; gap:12px;
        background:#fafaf8; border:1px solid #ede9e1;
        border-radius:12px; padding:12px 16px;
        margin-bottom:10px;
    }
    .receipt-preview img { width:48px; height:48px; object-fit:cover; border-radius:8px; }
    .receipt-preview a   { font-size:13px; font-weight:600; color:#2EB872; text-decoration:none; }
    .receipt-preview a:hover { text-decoration:underline; }

    .remove-label { display:flex; align-items:center; gap:6px; font-size:13px; color:#e05c5c; font-weight:500; cursor:pointer; }

    .btn-row   { display:flex; gap:10px; margin-top:8px; }
    .submit-btn {
        flex:1;
        background:#FBCF97; border:none; border-radius:13px;
        padding:13px; font-family:'Syne',sans-serif;
        font-size:14px; font-weight:700; color:#1C1C1E;
        cursor:pointer; transition:all 0.2s;
        display:flex; align-items:center; justify-content:center; gap:8px;
    }
    .submit-btn:hover { background:#f7bc71; transform:translateY(-1px); }

    .delete-btn {
        background:#fff; border:1.5px solid #fca5a5;
        border-radius:13px; padding:13px 20px;
        font-size:13px; font-weight:700; color:#ef4444;
        cursor:pointer; transition:all 0.15s;
    }
    .delete-btn:hover { background:#fef2f2; }

    .flash { padding:14px 20px; border-radius:14px; margin-bottom:20px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:10px; }
    .flash.success { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
    .flash.error   { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
</style>
@endpush

@section('content')
<main class="main-content">

    @if(session('success'))
        <div class="flash success">✅ {{ session('success') }}</div>
    @endif

    <div class="page-header">
        <div>
            <div class="page-title">Edit Transaction</div>
        </div>
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
                    <div class="type-opt {{ $transaction->type === 'Income' ? 'active-income' : '' }}"
                         id="opt-income" onclick="setType('Income')">💰 Income</div>
                </div>
                <input type="hidden" name="type" id="type-input" value="{{ old('type', $transaction->type) }}">
                @error('type') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Amount --}}
            <div class="form-group">
                <label class="form-label">Amount ($)</label>
                <input type="number" name="amount" step="0.01" min="0.01"
                       value="{{ old('amount', $transaction->amount) }}" class="form-input" required>
                @error('amount') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="description"
                       value="{{ old('description', $transaction->description) }}" class="form-input" required>
                @error('description') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Category --}}
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Date --}}
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="date"
                       value="{{ old('date', $transaction->date->format('Y-m-d')) }}" class="form-input" required>
                @error('date') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Receipt --}}
            <div class="form-group">
                <label class="form-label">Receipt Image</label>

                @if($transaction->receipt_image_path)
                    <div class="receipt-preview">
                        <img src="{{ $transaction->receipt_url }}" alt="Receipt">
                        <div>
                            <a href="{{ $transaction->receipt_url }}" target="_blank">View current receipt</a>
                            <div style="font-size:11px;color:#bbb;margin-top:2px;">Upload a new file to replace it</div>
                        </div>
                    </div>
                    <label class="remove-label">
                        <input type="checkbox" name="remove_receipt" value="1" {{ old('remove_receipt') ? 'checked' : '' }}>
                        Remove current receipt
                    </label>
                @endif

                <input type="file" name="receipt_image" accept="image/*" class="form-input" style="padding:8px 14px;margin-top:8px;">
                @error('receipt_image') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="btn-row">
                <button type="submit" class="submit-btn">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17,21 17,13 7,13 7,21"/><polyline points="7,3 7,8 15,8"/></svg>
                    Save Changes
                </button>

                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}"
                      onsubmit="return confirm('Permanently delete this transaction?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="delete-btn">🗑️ Delete</button>
                </form>
            </div>
        </form>
    </div>

</main>
@endsection

@push('scripts')
<script>
    function setType(val) {
        document.getElementById('type-input').value = val;
        document.getElementById('opt-expense').className = 'type-opt' + (val === 'Expense' ? ' active-expense' : '');
        document.getElementById('opt-income').className  = 'type-opt' + (val === 'Income'  ? ' active-income'  : '');
    }
</script>
@endpush