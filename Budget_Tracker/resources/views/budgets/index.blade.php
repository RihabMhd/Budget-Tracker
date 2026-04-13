@extends('layouts.app')

@section('title', 'Budgets')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/budgets.css', 'resources/js/budgets.js'])
@endpush

@section('content')
<main class="main-content">

    @if(session('success'))
        <div class="flash">✅ {{ session('success') }}</div>
    @endif

    <div class="page-header">
        <div>
            <div class="page-title">Budgets</div>
            <div class="page-sub">Set limits per category — {{ now()->format('F Y') }}</div>
        </div>
    </div>

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
                <span style="font-size:12px;color:#bbb;">{{ $budgets->count() }} set</span>
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
                                {{ number_format($spent, 2) }} DH spent
                                @if($budget) · limit {{ number_format($limit, 2) }} DH @endif
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

                    {{-- Inline Edit Form --}}
                    <div class="inline-edit" id="edit-{{ $cat->id }}" style="display:none;">
                        <form method="POST" action="{{ route('budgets.storeCategory') }}" class="edit-form-flex">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $cat->id }}">
                            <input type="number" name="monthly_limit" step="0.01" min="1"
                                   value="{{ $budget?->monthly_limit }}" required>
                            <button type="submit" class="save-btn">Save</button>
                            <button type="button" class="cancel-btn" onclick="toggleEdit({{ $cat->id }})">Cancel</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-msg">No categories found.</p>
            @endforelse
        </div>

        {{-- Add / Update Sidebar --}}
        <div class="add-panel">
            <div class="panel">
                <div class="panel-title" style="margin-bottom:20px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Quick Add
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
                    </div>

                    <div class="form-group">
                        <label class="form-label">Monthly Limit (DH)</label>
                        <input type="number" name="monthly_limit" class="form-input" placeholder="e.g. 500" step="0.01" min="1" required>
                    </div>

                    <button type="submit" class="btn-add">Save Budget</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection