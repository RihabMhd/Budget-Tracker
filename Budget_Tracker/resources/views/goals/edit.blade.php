@extends('layouts.app')

@section('title', 'Edit Goal')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/transactions.css'])
@endpush

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="page-title">Edit Goal</div>
        <a href="{{ route('goals.index') }}" class="back-btn">← Back to Goals</a>
    </div>

    <div class="edit-card">
        <form method="POST" action="{{ route('goals.update', $goal) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Goal Title</label>
                <input type="text" name="name" value="{{ old('name', $goal->name) }}" class="form-input" required>
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Target Amount (MAD)</label>
                <input type="number" name="target_amount" step="0.01" value="{{ old('target_amount', $goal->target_amount) }}" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Currently Saved (MAD)</label>
                <input type="number" name="current_amount" step="0.01" value="{{ old('current_amount', $goal->current_amount) }}" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline', \Carbon\Carbon::parse($goal->deadline)->format('Y-m-d')) }}" class="form-input" required>
            </div>

            <div class="btn-row">
                <button type="submit" class="submit-btn">
                    Save Changes
                </button>
                <button type="button" class="delete-btn" data-submit-form="delete-goal-form">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                </button>
            </div>
        </form>

        <form id="delete-goal-form" method="POST" action="{{ route('goals.destroy', $goal) }}">
            @csrf @method('DELETE')
        </form>
    </div>
</main>
@endsection

@push('scripts')
    @vite('resources/js/transactions.js')
@endpush