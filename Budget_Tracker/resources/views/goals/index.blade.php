@extends('layouts.app')

@section('title', 'Savings Goals')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/profile.css', 'resources/css/transactions.css'])
    <style>
        /* ── Responsive: Savings Goals ── */

        /* Page header: title + button side-by-side → stacked on mobile */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Goals grid: 3 cols → 2 → 1 */
        .goals-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        /* Goal card footer: input + buttons in a row */
        .goal-card-footer {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Modal sheet behaviour on mobile */
        @media (max-width: 1100px) {
            .goals-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .main-content {
                margin-left: 0;
                padding: 20px 16px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header .add-btn {
                width: 100%;
                justify-content: center;
            }

            .goals-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            /* Two-col form row collapses to single column */
            .two-col {
                grid-template-columns: 1fr !important;
            }

            /* Modal slides up from bottom */
            .modal-overlay {
                align-items: flex-end !important;
                padding: 0 !important;
            }

            .modal-box {
                border-radius: 24px 24px 0 0 !important;
                max-height: 92vh;
                overflow-y: auto;
                padding: 24px 20px !important;
            }

            /* Goal card footer: stack form + buttons */
            .goal-card-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .goal-card-footer form {
                flex: unset !important;
                width: 100%;
            }

            .goal-card-footer .action-btn {
                width: 100% !important;
                height: 38px !important;
                justify-content: center;
            }
        }

        @media (max-width: 420px) {
            .goal-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .goal-deadline-badge {
                align-self: flex-start;
            }
        }
    </style>
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
            <span data-reopen-modal="goal-modal" style="display:none;"></span>
        @endif

        <div class="page-header">
            <div>
                <div class="page-title">Savings Goals</div>
                <div class="page-sub">Track your progress and reach your targets</div>
            </div>
            <button class="add-btn" data-open-modal="goal-modal">
                <svg viewBox="0 0 24 24"
                    style="width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                New Goal
            </button>
        </div>

        <div class="goals-grid">
            @forelse($goals as $goal)
                <div class="goal-card">
                    <div class="goal-card-header">
                        <div>
                            <div class="goal-title">{{ $goal->name }}</div>
                            <div style="font-size: 12px; color: #aaa; margin-top: 4px;">
                                Target: {{ number_format($goal->target_amount, 2) }} MAD
                            </div>
                        </div>
                        <div class="goal-deadline-badge">
                            {{ \Carbon\Carbon::parse($goal->deadline)->diffForHumans() }}
                        </div>
                    </div>

                    <div class="goal-progress-container">
                        <div class="progress-info">
                            <span style="font-weight: 700; color: #2EB872;">{{ $goal->progress_percent }}% Saved</span>
                            <span style="color: #666;">{{ number_format($goal->current_amount, 2) }} MAD</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $goal->progress_percent }}%"></div>
                        </div>
                    </div>

                    <div class="goal-card-footer">
                        {{-- Quick Add Funds Form --}}
                        <form action="{{ route('goals.funds', $goal) }}" method="POST"
                            style="display:flex; flex:1; gap:8px;">
                            @csrf
                            <input type="number" name="amount" step="0.01" placeholder="+ Amount"
                                class="quick-add-input" style="flex:1; min-width:0;">
                            <button type="submit" class="action-btn"
                                style="background: #e8f5ed; color: #2EB872; border: none; width: 38px; height: 38px; flex-shrink:0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </button>
                        </form>

                        <a href="{{ route('goals.edit', $goal) }}" class="action-btn"
                            style="width: 38px; height: 38px; flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align:center; padding: 60px;">
                    <div style="font-family:'Syne',sans-serif; font-weight:700; color:#ccc;">No goals set yet. Start saving
                        today!</div>
                </div>
            @endforelse
        </div>
    </main>

    {{-- ══ CREATE GOAL MODAL ══ --}}
    <div id="goal-modal" class="modal-overlay {{ $errors->any() ? '' : 'hidden' }}" data-dismiss-modal="goal-modal">
        <div class="modal-box" onclick="event.stopPropagation()">
            <button class="modal-close" data-close-modal="goal-modal">✕</button>
            <div class="modal-title">New Savings Goal</div>
            <div class="modal-sub">Set a target and track your progress</div>

            <form method="POST" action="{{ route('goals.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Goal Title</label>
                    <input type="text" name="name" placeholder="e.g. New MacBook Pro" value="{{ old('name') }}"
                        class="form-input" required>
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="two-col" style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">
                    <div class="form-group">
                        <label class="form-label">Target Amount (MAD)</label>
                        <input type="number" name="target_amount" step="0.01" placeholder="0.00"
                            value="{{ old('target_amount') }}" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Starting Balance</label>
                        <input type="number" name="current_amount" step="0.01"
                            value="{{ old('current_amount', 0) }}" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Date</label>
                    <input type="date" name="deadline" value="{{ old('deadline') }}" class="form-input" required>
                    @error('deadline')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="submit-btn"
                    style="width: 100%; justify-content: center; margin-top: 10px;">
                    Create Goal
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/transactions.js')
@endpush