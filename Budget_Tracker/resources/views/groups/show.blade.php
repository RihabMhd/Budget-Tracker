@extends('layouts.app')

@section('title', $group->name)

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/transactions.css'])
@endpush

@section('content')
<main class="main-content">
    <div class="page-header">
        <div>
            <div class="page-title">{{ $group->name }}</div>
            <div class="page-sub">Invite Code: <span style="color: #2EB872; font-weight: 700;">{{ $group->invite_code }}</span></div>
        </div>
        <button class="add-btn" data-open-modal="expense-modal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Shared Expense
        </button>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-label">People Owe Me</div>
            <div class="stat-value" style="color: #2EB872;">
                {{ number_format($what_people_owe_me, 2) }} <span class="currency">MAD</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">I Owe Others</div>
            <div class="stat-value" style="color: #ff4d4d;">
                {{ number_format($what_i_owe, 2) }} <span class="currency">MAD</span>
            </div>
        </div>
    </div>

    <div class="two-col">
        <div class="col-left">
            <div class="section-title" style="margin-bottom: 20px;">Group Members</div>
            <div class="goal-card" style="padding: 20px;">
                @foreach($group->members as $member)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                    <span class="goal-title">{{ $member->username }}</span>
                    <span class="goal-deadline-badge">{{ $member->pivot->role }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-right">
            <div class="section-title" style="margin-bottom: 20px;">Group Activity</div>
            <div class="transactions-card">
                <div class="table-container">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th>Your Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_transactions as $tx)
                            <tr>
                                <td><span class="category-pill" style="background: #f5f3ee; color: #1a1a1a;">{{ $tx->user->username }}</span></td>
                                <td class="td-desc">{{ $tx->description }}</td>
                                <td class="td-amount">{{ number_format($tx->amount, 2) }}</td>
                                <td class="td-amount" style="color: #888;">{{ number_format($tx->amount / $group->members->count(), 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<div id="expense-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">New Shared Purchase</div>
            <button class="close-modal">&times;</button>
        </div>
        <form action="{{ route('groups.transactions.store', $group) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Amount (MAD)</label>
                <input type="number" name="amount" step="0.01" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="description" placeholder="e.g., Grocery Shopping" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-input">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="submit-btn" style="width: 100%; margin-top: 15px;">Split with Group</button>
        </form>
    </div>
</div>
@endsection