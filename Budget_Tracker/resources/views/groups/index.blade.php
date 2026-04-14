@extends('layouts.app')

@section('title', 'Collective Groups')

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/dashboard.css'])
    <style>
        /* ── Page header ── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .page-title {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.5px;
        }

        .page-sub {
            font-size: 14px;
            color: #888;
            margin-top: 4px;
        }

        /* ── Header buttons ── */
        .btn-join {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 14px;
            border: 1.5px solid #e0ddd5;
            background: #f5f3ee;
            font-family: 'Syne', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #1a1a1a;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-join:hover {
            border-color: #FBCF97;
            box-shadow: 0 0 0 3px rgba(251,207,151,0.2);
        }

        .btn-new {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 14px;
            border: none;
            background: #FBCF97;
            font-family: 'Syne', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #1C1C1E;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-new:hover {
            background: #f7bc71;
            transform: translateY(-1px);
        }

        .btn-new svg {
            width: 15px;
            height: 15px;
            stroke: #1C1C1E;
            fill: none;
            stroke-width: 3;
            stroke-linecap: round;
        }

        /* ── Empty state ── */
        .empty-state {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #ede9e1;
            padding: 64px 32px;
            text-align: center;
            animation: fadeUp 0.4s ease both;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: #f5f3ee;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .empty-icon svg {
            width: 28px;
            height: 28px;
            stroke: #bbb;
            fill: none;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .empty-title {
            font-family: 'Syne', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 6px;
        }

        .empty-text {
            font-size: 13px;
            color: #aaa;
        }

        /* ── Group cards grid ── */
        .groups-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 18px;
        }

        /* ── Group card ── */
        .group-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #ede9e1;
            padding: 24px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            animation: fadeUp 0.4s ease both;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: block;
        }

        .group-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(251, 207, 151, 0.10);
        }

        .group-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 36px rgba(0,0,0,0.08);
            border-color: #FBCF97;
        }

        .group-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .group-avatar {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: #f5f3ee;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #1a1a1a;
            flex-shrink: 0;
        }

        .group-member-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #f5f3ee;
            border-radius: 99px;
            padding: 5px 11px;
            font-size: 12px;
            font-weight: 600;
            color: #888;
        }

        .group-member-badge svg {
            width: 13px;
            height: 13px;
            stroke: #bbb;
            fill: none;
            stroke-width: 2;
        }

        .group-name {
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 2px;
        }

        .group-code {
            font-size: 12px;
            color: #bbb;
        }

        .group-code strong {
            color: #2EB872;
            font-weight: 700;
        }

        .group-balance-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid #f4f1eb;
        }

        .group-balance-label {
            font-size: 11px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 500;
        }

        .group-balance-value {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #1a1a1a;
        }

        .group-view-link {
            font-size: 12px;
            font-weight: 700;
            color: #2EB872;
        }

        /* ── Modal overlay ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 200;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 24px;
            padding: 32px;
            width: 100%;
            max-width: 440px;
            position: relative;
            animation: modalUp 0.25s ease;
        }

        @keyframes modalUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 22px;
            color: #bbb;
            cursor: pointer;
            line-height: 1;
        }

        .modal-close:hover {
            color: #888;
        }

        .modal-title {
            font-family: 'Syne', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .modal-sub {
            font-size: 13px;
            color: #aaa;
            margin-bottom: 24px;
        }
    </style>
@endpush

@section('content')
<main class="main-content">

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <div class="page-title">Collective Groups</div>
            <div class="page-sub">Manage shared expenses with your roommates or friends</div>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <button class="btn-join" onclick="openModal('join-modal')">
                Join Group
            </button>
            <button class="btn-new" onclick="openModal('create-modal')">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Group
            </button>
        </div>
    </div>

    {{-- ── Groups Grid ── --}}
    @if($groups->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="empty-title">No groups yet</div>
            <div class="empty-text">Create a group or join one with an invite code to get started.</div>
        </div>
    @else
        <div class="groups-grid">
            @foreach($groups as $group)
            <a class="group-card" href="{{ route('groups.show', $group) }}" style="animation-delay: {{ $loop->index * 0.06 }}s;">
                <div class="group-card-top">
                    <div class="group-avatar">{{ strtoupper(substr($group->name, 0, 1)) }}</div>
                    <div class="group-member-badge">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        {{ $group->members_count }} members
                    </div>
                </div>

                <div class="group-name">{{ $group->name }}</div>
                <div class="group-code">Code: <strong>{{ $group->invite_code }}</strong></div>

                <div class="group-balance-row">
                    <div>
                        <div class="group-balance-label">Collective Balance</div>
                        <div class="group-balance-value">{{ number_format($group->calculateTotalBalance(), 2) }} <span style="font-size:13px;font-weight:500;color:#aaa;">MAD</span></div>
                    </div>
                    <div class="group-view-link">View →</div>
                </div>
            </a>
            @endforeach
        </div>
    @endif

</main>

{{-- ── Create Group Modal ── --}}
<div class="modal-overlay" id="create-modal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('create-modal')">&times;</button>
        <div class="modal-title">Start a New Group</div>
        <div class="modal-sub">You'll become the Admin and receive an invite code.</div>

        <form action="{{ route('groups.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Group Name</label>
                <input type="text" name="name" class="form-input" placeholder="e.g., Green Colocation" required>
            </div>
            <button type="submit" class="submit-btn" style="margin-top:8px;">
                Create &amp; Become Owner
            </button>
        </form>
    </div>
</div>

{{-- ── Join Group Modal ── --}}
<div class="modal-overlay" id="join-modal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('join-modal')">&times;</button>
        <div class="modal-title">Join Existing Group</div>
        <div class="modal-sub">Enter the invite code shared by your group Admin.</div>

        <form action="{{ route('groups.join') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Invite Code</label>
                <input type="text" name="invite_code" class="form-input"
                    placeholder="XYZ123"
                    style="text-align:center;text-transform:uppercase;letter-spacing:3px;font-weight:700;font-size:16px;"
                    required>
            </div>
            <button type="submit" class="submit-btn" style="margin-top:8px;background:#1a1a1a;color:#fff;">
                Join Now
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }
    // Close on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) closeModal(overlay.id);
        });
    });
    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active')
                .forEach(m => m.classList.remove('active'));
        }
    });
</script>
@endpush
@endsection