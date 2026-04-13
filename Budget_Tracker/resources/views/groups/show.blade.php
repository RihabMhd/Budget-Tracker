@extends('layouts.app')

@section('title', $group->name)

@push('styles')
    @vite(['resources/css/app.css', 'resources/css/dashboard.css'])
    <style>
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 32px;
            gap: 16px;
        }
        .page-title {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.5px;
        }
        .page-sub { font-size: 14px; color: #888; margin-top: 4px; }
        .page-sub strong { color: #2EB872; font-weight: 700; }

        .header-actions { display: flex; gap: 10px; align-items: center; flex-shrink: 0; }

        .btn-invite {
            display: flex; align-items: center; gap: 8px;
            padding: 11px 18px; border-radius: 14px;
            border: 1.5px solid #e0ddd5; background: #f5f3ee;
            font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; color: #1a1a1a;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-invite:hover { border-color: #2EB872; box-shadow: 0 0 0 3px rgba(46,184,114,0.12); }
        .btn-invite svg { width: 14px; height: 14px; stroke: #888; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        .btn-add {
            display: flex; align-items: center; gap: 8px;
            padding: 11px 20px; border-radius: 14px;
            border: none; background: #FBCF97;
            font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; color: #1C1C1E;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-add:hover { background: #f7bc71; transform: translateY(-1px); }
        .btn-add svg { width: 15px; height: 15px; stroke: #1C1C1E; fill: none; stroke-width: 3; stroke-linecap: round; }

        /* ── Balance cards ── */
        .balance-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 16px; margin-bottom: 24px;
        }
        .balance-card {
            background: #fff; border-radius: 20px; border: 1px solid #ede9e1;
            padding: 22px 24px; animation: fadeUp 0.35s ease both;
            position: relative; overflow: hidden;
        }
        .balance-card.green { border-left: 4px solid #2EB872; }
        .balance-card.red   { border-left: 4px solid #e05c5c; }
        .balance-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; color: #aaa; margin-bottom: 8px; }
        .balance-value { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 14px; }
        .balance-card.green .balance-value { color: #2EB872; }
        .balance-card.red   .balance-value { color: #e05c5c; }
        .balance-currency { font-size: 14px; font-weight: 500; opacity: 0.6; }

        /* Per-person debt rows inside balance card */
        .debt-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 0; border-top: 1px solid #f4f1eb;
        }
        .debt-avatar {
            width: 30px; height: 30px; border-radius: 9px; background: #f5f3ee;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 800; color: #1a1a1a; flex-shrink: 0;
        }
        .debt-name { font-size: 13px; font-weight: 600; color: #1a1a1a; flex: 1; }
        .debt-amount { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 800; }
        .balance-card.green .debt-amount { color: #2EB872; }
        .balance-card.red   .debt-amount { color: #e05c5c; }
        .balance-empty { font-size: 12px; color: #ccc; padding-top: 10px; border-top: 1px solid #f4f1eb; }

        /* Pay button inside debt row */
        .pay-btn {
            padding: 5px 13px; border-radius: 99px; border: none;
            background: #2EB872; color: #fff;
            font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700;
            cursor: pointer; transition: all 0.2s; white-space: nowrap; flex-shrink: 0;
        }
        .pay-btn:hover { background: #24a060; transform: translateY(-1px); }

        /* Member action buttons (kick / transfer) */
        .member-actions { display: flex; gap: 6px; align-items: center; margin-left: 8px; }
        .action-btn {
            width: 30px; height: 30px; border-radius: 9px; border: 1.5px solid #e8e4dc;
            background: #f5f3ee; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; padding: 0;
        }
        .action-btn svg { width: 13px; height: 13px; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .transfer-btn svg { stroke: #888; }
        .transfer-btn:hover { border-color: #FBCF97; background: #fff9f0; }
        .transfer-btn:hover svg { stroke: #9a5a10; }
        .kick-btn svg { stroke: #e05c5c; }
        .kick-btn:hover { border-color: #e05c5c; background: #fff2f2; }

        /* ── Two-col layout ── */
        .detail-grid { display: grid; grid-template-columns: 320px 1fr; gap: 20px; align-items: start; }

        /* ── Panel ── */
        .panel {
            background: #fff; border-radius: 20px; border: 1px solid #ede9e1;
            padding: 24px; animation: fadeUp 0.4s ease 0.1s both;
        }
        .panel-title {
            font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700; color: #1a1a1a;
            margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;
        }
        .count-pill {
            background: #f5f3ee; border-radius: 99px; padding: 3px 10px;
            font-size: 12px; font-weight: 600; color: #888; font-family: 'DM Sans', sans-serif;
        }

        /* ── Member rows ── */
        .member-row {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 0; border-bottom: 1px solid #f4f1eb;
        }
        .member-row:last-child { border-bottom: none; padding-bottom: 0; }
        .member-avatar {
            width: 38px; height: 38px; border-radius: 12px; background: #f5f3ee;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 800; color: #1a1a1a; flex-shrink: 0;
        }
        .member-name { font-size: 14px; font-weight: 600; color: #1a1a1a; }
        .member-since { font-size: 11px; color: #bbb; margin-top: 1px; }
        .role-badge {
            font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 99px;
            text-transform: uppercase; letter-spacing: 0.3px;
        }
        .role-badge.admin  { background: #fff9f0; color: #9a5a10; border: 1px solid #FBCF97; }
        .role-badge.member { background: #f5f3ee; color: #888;    border: 1px solid #e8e4dc; }

        /* ── Activity table ── */
        .activity-panel { animation-delay: 0.18s; }
        .activity-empty { text-align: center; padding: 48px 20px; color: #bbb; font-size: 13px; }
        .activity-empty-icon { font-size: 32px; margin-bottom: 10px; }

        .activity-table { width: 100%; border-collapse: collapse; }
        .activity-table th {
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px;
            color: #bbb; padding: 0 12px 12px 0; text-align: left; border-bottom: 1px solid #f4f1eb;
        }
        .activity-table th:last-child { text-align: right; padding-right: 0; }
        .activity-table td { padding: 13px 12px 13px 0; border-bottom: 1px solid #f4f1eb; vertical-align: middle; }
        .activity-table tr:last-child td { border-bottom: none; }
        .activity-table td:last-child { padding-right: 0; }

        .tx-who {
            display: inline-flex; align-items: center; gap: 6px;
            background: #f5f3ee; border-radius: 99px; padding: 4px 10px;
            font-size: 12px; font-weight: 600; color: #555; white-space: nowrap;
        }
        .tx-who-dot { width: 6px; height: 6px; border-radius: 50%; background: #2EB872; flex-shrink: 0; }
        .tx-who-dot.self { background: #FBCF97; }

        .tx-desc { font-size: 13px; font-weight: 500; color: #1a1a1a; }
        .tx-date { font-size: 11px; color: #bbb; margin-top: 2px; }

        .tx-total { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; color: #1a1a1a; white-space: nowrap; }
        .tx-total-sub { font-size: 11px; color: #bbb; }

        .tx-share { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; text-align: right; white-space: nowrap; }
        .tx-share.owe  { color: #e05c5c; }
        .tx-share.paid { color: #2EB872; font-size: 11px; font-weight: 600; background: #f0fdf4; padding: 3px 8px; border-radius: 99px; }
        .tx-share.none { color: #ddd; }

        /* ── Modal ── */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.45);
            z-index: 200; display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: #fff; border-radius: 24px; padding: 32px;
            width: 100%; max-width: 440px; position: relative; animation: modalUp 0.25s ease;
        }
        @keyframes modalUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .modal-close {
            position: absolute; top: 20px; right: 20px;
            background: none; border: none; font-size: 22px; color: #bbb; cursor: pointer;
        }
        .modal-close:hover { color: #888; }
        .modal-title { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
        .modal-sub { font-size: 13px; color: #aaa; margin-bottom: 24px; }

        /* ── Toast ── */
        #copy-toast {
            position: fixed; bottom: 32px; left: 50%; transform: translateX(-50%);
            background: #1a1a1a; color: #fff; padding: 10px 22px; border-radius: 99px;
            font-size: 13px; font-weight: 600; opacity: 0; pointer-events: none;
            transition: opacity 0.3s; z-index: 999;
        }
        #copy-toast.show { opacity: 1; }

        @media (max-width: 900px) {
            .detail-grid { grid-template-columns: 1fr; }
            .balance-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
<main class="main-content">

    {{-- ── Header ── --}}
    <div class="page-header">
        <div>
            <div class="page-title">{{ $group->name }}</div>
            <div class="page-sub">
                Invite code: <strong>{{ $group->invite_code }}</strong>
                &nbsp;·&nbsp; {{ $group->members->count() }} {{ Str::plural('member', $group->members->count()) }}
            </div>
        </div>
        <div class="header-actions">
            <button class="btn-invite" onclick="copyInvite('{{ $group->invite_code }}')">
                <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Copy Invite
            </button>
            <button class="btn-add" onclick="openModal('expense-modal')">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Shared Expense
            </button>
        </div>
    </div>

    {{-- ── Balance cards ── --}}
    <div class="balance-grid">

        {{-- People who owe ME --}}
        <div class="balance-card green" style="animation-delay:0.05s;">
            <div class="balance-label">People Owe Me</div>
            <div class="balance-value">
                {{ number_format($what_people_owe_me, 2) }}
                <span class="balance-currency">MAD</span>
            </div>
            @if(count($owes_me) > 0)
                @foreach($owes_me as $debt)
                <div class="debt-row">
                    <div class="debt-avatar">{{ strtoupper(substr($debt['user']->username, 0, 1)) }}</div>
                    <div class="debt-name">{{ $debt['user']->username }}</div>
                    <div class="debt-amount">{{ number_format($debt['amount'], 2) }} MAD</div>
                </div>
                @endforeach
            @else
                <div class="balance-empty">Nobody owes you anything yet.</div>
            @endif
        </div>

        {{-- People I OWE --}}
        <div class="balance-card red" style="animation-delay:0.1s;">
            <div class="balance-label">I Owe Others</div>
            <div class="balance-value">
                {{ number_format($what_i_owe, 2) }}
                <span class="balance-currency">MAD</span>
            </div>
            @if(count($i_owe) > 0)
                @foreach($i_owe as $debt)
                <div class="debt-row">
                    <div class="debt-avatar">{{ strtoupper(substr($debt['user']->username, 0, 1)) }}</div>
                    <div class="debt-name">{{ $debt['user']->username }}</div>
                    <div class="debt-amount">{{ number_format($debt['amount'], 2) }} MAD</div>
                    <form method="POST"
                          action="{{ route('groups.members.settle', [$group, $debt['user']]) }}"
                          onsubmit="return confirm('Pay {{ number_format($debt['amount'], 2) }} MAD to {{ $debt['user']->username }}?')">
                        @csrf
                        <button type="submit" class="pay-btn">Pay</button>
                    </form>
                </div>
                @endforeach
            @else
                <div class="balance-empty">You don't owe anyone anything.</div>
            @endif
        </div>

    </div>

    {{-- ── Detail grid ── --}}
    <div class="detail-grid">

        {{-- Members --}}
        <div class="panel">
            <div class="panel-title">
                Group Members
                <span class="count-pill">{{ $group->members->count() }}</span>
            </div>

            @php $isOwner = auth()->id() === $group->owner_id; @endphp

            @foreach($group->members as $member)
            @php $isSelf = $member->id === auth()->id(); @endphp
            <div class="member-row">
                <div class="member-avatar">{{ strtoupper(substr($member->username, 0, 1)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="member-name">
                        {{ $member->username }}
                        @if($isSelf) <span style="font-size:11px;color:#bbb;">(you)</span> @endif
                    </div>
                    <div class="member-since">
                        Joined {{ \Carbon\Carbon::parse($member->pivot->joined_at)->diffForHumans() }}
                    </div>
                </div>
                <span class="role-badge {{ strtolower($member->pivot->role) }}">{{ $member->pivot->role }}</span>

                {{-- Actions: only show for other members, only to owner --}}
                @if($isOwner && !$isSelf)
                <div class="member-actions">
                    {{-- Transfer ownership --}}
                    <form method="POST"
                          action="{{ route('groups.members.transfer', [$group, $member]) }}"
                          onsubmit="return confirm('Transfer ownership to {{ $member->username }}? You will become a regular member.')">
                        @csrf
                        <button type="submit" class="action-btn transfer-btn" title="Transfer ownership">
                            <svg viewBox="0 0 24 24"><path d="M16 3l4 4-4 4"/><path d="M20 7H4"/><path d="M8 21l-4-4 4-4"/><path d="M4 17h16"/></svg>
                        </button>
                    </form>
                    {{-- Kick member --}}
                    <form method="POST"
                          action="{{ route('groups.members.destroy', [$group, $member]) }}"
                          onsubmit="return confirm('Remove {{ $member->username }} from the group?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn kick-btn" title="Kick member">
                            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Activity --}}
        <div class="panel activity-panel">
            <div class="panel-title">Group Activity</div>

            @if($recent_transactions->isEmpty())
                <div class="activity-empty">
                    <div class="activity-empty-icon">🧾</div>
                    No shared expenses yet — add the first one!
                </div>
            @else
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Paid by</th>
                            <th>Description</th>
                            <th>Total</th>
                            <th>Your share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_transactions as $tx)
                        @php
                            $mySplit = $tx->expenseSplits->firstWhere('user_id', auth()->id());
                            $iPaid   = $tx->user_id === auth()->id();
                        @endphp
                        <tr>
                            <td>
                                <span class="tx-who">
                                    <span class="tx-who-dot {{ $iPaid ? 'self' : '' }}"></span>
                                    {{ $tx->user->username }}
                                </span>
                            </td>
                            <td>
                                <div class="tx-desc">{{ $tx->description }}</div>
                                <div class="tx-date">{{ $tx->date->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div class="tx-total">{{ number_format($tx->amount, 2) }}</div>
                                <div class="tx-total-sub">MAD</div>
                            </td>
                            <td style="text-align:right;">
                                @if($iPaid)
                                    <span class="tx-share paid">You paid</span>
                                @elseif($mySplit)
                                    <span class="tx-share owe">{{ number_format($mySplit->amount_share, 2) }} MAD</span>
                                @else
                                    <span class="tx-share none">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</main>

{{-- ── Add Expense Modal ── --}}
<div class="modal-overlay" id="expense-modal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('expense-modal')">&times;</button>
        <div class="modal-title">New Shared Purchase</div>
        <div class="modal-sub">Cost will be split equally among all members.</div>

        <form action="{{ route('groups.transactions.store', $group) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Amount (MAD)</label>
                <input type="number" name="amount" step="0.01" min="0.01" class="form-input" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-input" placeholder="e.g., Grocery Shopping" required>
            </div>
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-input">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-input" value="{{ date('Y-m-d') }}">
            </div>
            <button type="submit" class="submit-btn" style="margin-top:8px;">Split with Group</button>
        </form>
    </div>
</div>

<div id="copy-toast">✓ Invite code copied!</div>

@push('scripts')
<script>
    function openModal(id)  { document.getElementById(id).classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }

    document.querySelectorAll('.modal-overlay').forEach(o =>
        o.addEventListener('click', e => { if (e.target === o) closeModal(o.id); })
    );
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape')
            document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
    });

    function copyInvite(code) {
        navigator.clipboard.writeText(code).then(() => {
            const t = document.getElementById('copy-toast');
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2200);
        });
    }
</script>
@endpush
@endsection