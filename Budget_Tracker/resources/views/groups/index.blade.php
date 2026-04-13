@extends('layouts.app')

@section('title', 'Collective Groups')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/transactions.css'])
@endpush

@section('content')
<main class="main-content">
    <div class="page-header">
        <div>
            <div class="page-title">Collective Groups</div>
            <div class="page-sub">Manage shared expenses with your roommates or friends</div>
        </div>
        <div style="display: flex; gap: 12px;">
            <button class="add-btn" style="background: #f5f3ee; color: #1a1a1a; border: 1px solid #e0ddd5;" data-open-modal="join-group-modal">
                Join Group
            </button>
            <button class="add-btn" data-open-modal="create-group-modal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                New Group
            </button>
        </div>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
        @foreach($groups as $group)
        <div class="goal-card" style="cursor: pointer;" onclick="window.location='{{ route('groups.show', $group) }}'">
            <div class="goal-card-header">
                <div class="goal-title">{{ $group->name }}</div>
                <div class="goal-deadline-badge">{{ $group->members_count }} Members</div>
            </div>
            
            <div class="goal-progress-container">
                <div class="progress-info">
                    <span>Collective Balance</span>
                    <span style="font-weight: 700;">{{ number_format($group->calculateTotalBalance(), 2) }} MAD</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: 100%;"></div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                <span style="font-size: 11px; color: #888;">Code: <strong>{{ $group->invite_code }}</strong></span>
                <a href="{{ route('groups.show', $group) }}" style="color: #2EB872; font-weight: 700; font-size: 13px; text-decoration: none;">View Dashboard →</a>
            </div>
        </div>
        @endforeach
    </div>

    <div id="create-group-modal" class="modal">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <div class="modal-title">Start a New Group</div>
                <button class="close-modal" data-close-modal="create-group-modal">&times;</button>
            </div>
            <form action="{{ route('groups.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Group Name</label>
                    <input type="text" name="name" class="form-input" placeholder="e.g., Green Colocation" required>
                </div>
                <button type="submit" class="submit-btn" style="width: 100%; margin-top: 10px;">Create & Become Owner</button>
            </form>
        </div>
    </div>

    <div id="join-group-modal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <div class="modal-title">Join Existing Group</div>
                <button class="close-modal" data-close-modal="join-group-modal">&times;</button>
            </div>
            <form action="{{ route('groups.join') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Invite Code</label>
                    <input type="text" name="invite_code" class="form-input" placeholder="XYZ123" style="text-align: center; text-transform: uppercase;" required>
                </div>
                <button type="submit" class="submit-btn" style="width: 100%; background: #1a1a1a;">Join Now</button>
            </form>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Logic to open modals
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.onclick = () => {
            const id = btn.getAttribute('data-open-modal');
            document.getElementById(id).style.display = 'flex';
        };
    });

    // Logic to close modals
    document.querySelectorAll('.close-modal, .modal').forEach(el => {
        el.onclick = (e) => {
            if (e.target.classList.contains('close-modal') || e.target.classList.contains('modal')) {
                const modal = el.closest('.modal') || el;
                modal.style.display = 'none';
            }
        };
    });
</script>
@endpush