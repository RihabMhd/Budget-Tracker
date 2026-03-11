@extends('layouts.app')

@section('title', 'My Categories')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap');

    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'DM Sans', sans-serif; background: #F5F3EE; color: #1a1a1a; }
    h1, h2, h3 { font-family: 'Syne', sans-serif; }

    .main-content { margin-left: 255px; padding: 40px 48px; min-height: 100vh; }

    /* ── Flash ── */
    .flash {
        display: flex; align-items: center; gap: 10px;
        border-radius: 14px; padding: 14px 20px;
        font-size: 13px; font-weight: 600; margin-bottom: 28px;
        animation: fadeUp 0.35s ease both;
    }
    .flash.success { background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
    .flash.error   { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }

    /* ── Page header ── */
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 32px; animation: fadeUp 0.3s ease both; }
    .page-title  { font-size: 28px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
    .page-sub    { font-size: 14px; color: #999; margin-top: 3px; }

    /* ── Layout ── */
    .layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
        align-items: start;
    }

    /* ── Panel ── */
    .panel {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #ede9e1;
        padding: 28px;
        animation: fadeUp 0.4s ease 0.1s both;
    }

    .panel-title {
        font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700;
        color: #1a1a1a; margin-bottom: 20px;
        display: flex; align-items: center; justify-content: space-between;
    }
    .panel-count {
        font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 500;
        color: #bbb; background: #f5f3ee; border-radius: 99px; padding: 3px 10px;
    }

    /* ── System categories grid ── */
    .sys-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
        margin-bottom: 6px;
    }

    .sys-chip {
        display: flex; align-items: center; gap: 8px;
        padding: 9px 14px; border-radius: 12px;
        border: 1.5px solid #ede9e1; background: #fafaf8;
        font-size: 13px; font-weight: 500; color: #555;
    }

    .sys-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }

    /* ── Custom category rows ── */
    .cat-list { display: flex; flex-direction: column; gap: 2px; }

    .cat-row {
        display: flex; align-items: center; gap: 14px;
        padding: 13px 14px; border-radius: 14px;
        transition: background 0.15s;
    }
    .cat-row:hover { background: #fafaf8; }

    .cat-swatch {
        width: 40px; height: 40px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0;
        transition: transform 0.2s;
    }
    .cat-row:hover .cat-swatch { transform: scale(1.08); }

    .cat-info { flex: 1; min-width: 0; }
    .cat-name  { font-size: 14px; font-weight: 600; color: #1a1a1a; }
    .cat-meta  { font-size: 11px; color: #bbb; margin-top: 2px; }

    .cat-color-pill {
        display: flex; align-items: center; gap: 6px;
        background: #f5f3ee; border-radius: 99px;
        padding: 4px 10px 4px 6px; font-size: 11px;
        font-weight: 600; color: #888;
    }
    .cat-color-dot { width: 12px; height: 12px; border-radius: 50%; }

    .cat-actions { display: flex; gap: 6px; }

    .btn-edit {
        background: #f5f3ee; border: 1px solid #ede9e1; border-radius: 10px;
        padding: 7px 14px; font-size: 12px; font-weight: 600; color: #555;
        cursor: pointer; transition: all 0.15s; white-space: nowrap;
    }
    .btn-edit:hover { background: #FBCF97; border-color: #FBCF97; color: #1C1C1E; }

    .btn-del {
        background: none; border: 1px solid #fecaca; border-radius: 10px;
        padding: 7px 11px; font-size: 12px; color: #ef4444;
        cursor: pointer; transition: all 0.15s;
    }
    .btn-del:hover { background: #fee2e2; }

    /* ── Inline edit form ── */
    .inline-edit {
        display: none; margin-top: 8px; margin-left: 54px;
        background: #fafaf8; border: 1.5px solid #ede9e1;
        border-radius: 14px; padding: 16px;
    }
    .inline-edit.open { display: block; }

    .ie-row { display: flex; gap: 10px; align-items: center; margin-bottom: 12px; }
    .ie-input {
        flex: 1; padding: 10px 14px; border-radius: 12px;
        border: 1.5px solid #ede9e1; font-family: 'DM Sans', sans-serif;
        font-size: 14px; font-weight: 600; color: #1a1a1a;
        background: #fff; outline: none; transition: border-color 0.2s;
    }
    .ie-input:focus { border-color: #FBCF97; box-shadow: 0 0 0 3px rgba(251,207,151,0.15); }

    .ie-color-wrap { position: relative; }
    .ie-color-btn {
        width: 42px; height: 42px; border-radius: 12px;
        border: 2px solid #ede9e1; cursor: pointer;
        transition: border-color 0.2s; overflow: hidden; flex-shrink: 0;
    }
    .ie-color-input {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }

    .ie-btns { display: flex; gap: 8px; }
    .ie-save {
        background: #FBCF97; border: none; border-radius: 11px;
        padding: 9px 18px; font-family: 'Syne', sans-serif;
        font-size: 12px; font-weight: 700; color: #1C1C1E; cursor: pointer;
        transition: background 0.15s;
    }
    .ie-save:hover { background: #f7bc71; }
    .ie-cancel {
        background: none; border: 1px solid #e5e7eb; border-radius: 11px;
        padding: 9px 14px; font-size: 12px; color: #aaa; cursor: pointer;
    }

    /* ── Empty state ── */
    .empty-state {
        text-align: center; padding: 40px 20px;
        color: #bbb; font-size: 13px;
    }
    .empty-state .empty-icon { font-size: 40px; margin-bottom: 12px; }
    .empty-state p { font-size: 14px; color: #bbb; }

    /* ── Add form panel ── */
    .add-panel { position: sticky; top: 24px; }

    /* ── Form ── */
    .form-section-title {
        font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700;
        color: #1a1a1a; margin-bottom: 18px;
        display: flex; align-items: center; gap: 8px;
    }

    .form-group { margin-bottom: 18px; }
    .form-label {
        display: block; font-size: 11px; font-weight: 600; color: #999;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 7px;
    }

    .form-input {
        width: 100%; padding: 13px 16px; border-radius: 14px;
        border: 1.5px solid #ede9e1; font-family: 'DM Sans', sans-serif;
        font-size: 14px; color: #1a1a1a; background: #fafaf8;
        outline: none; transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus {
        border-color: #FBCF97; background: #fff;
        box-shadow: 0 0 0 3px rgba(251,207,151,0.2);
    }
    .form-error { font-size: 12px; color: #ef4444; margin-top: 5px; }
    .char-count { font-size: 11px; color: #bbb; text-align: right; margin-top: 4px; }

    /* ── Color picker ── */
    .color-field {
        display: flex; align-items: center; gap: 12px;
    }

    .color-preview-btn {
        width: 48px; height: 48px; border-radius: 14px;
        border: 2px solid #ede9e1; cursor: pointer;
        position: relative; overflow: hidden; flex-shrink: 0;
        transition: border-color 0.2s, transform 0.2s;
    }
    .color-preview-btn:hover { border-color: #FBCF97; transform: scale(1.05); }

    .color-native {
        position: absolute; inset: -4px; opacity: 0;
        cursor: pointer; width: calc(100% + 8px); height: calc(100% + 8px);
    }

    .color-hex-input {
        flex: 1; padding: 13px 16px; border-radius: 14px;
        border: 1.5px solid #ede9e1; font-family: 'DM Sans', sans-serif;
        font-size: 14px; font-weight: 600; color: #1a1a1a;
        background: #fafaf8; outline: none; letter-spacing: 1px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .color-hex-input:focus {
        border-color: #FBCF97; background: #fff;
        box-shadow: 0 0 0 3px rgba(251,207,151,0.2);
    }

    /* ── Palette swatches ── */
    .palette-label { font-size: 11px; color: #bbb; font-weight: 500; margin-bottom: 8px; }
    .palette {
        display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 6px;
    }
    .swatch {
        width: 28px; height: 28px; border-radius: 8px;
        cursor: pointer; border: 2px solid transparent;
        transition: transform 0.15s, border-color 0.15s;
    }
    .swatch:hover { transform: scale(1.2); }
    .swatch.selected { border-color: #1a1a1a; transform: scale(1.15); }

    /* ── Preview ── */
    .preview-box {
        background: #f5f3ee; border-radius: 14px; padding: 16px;
        display: flex; align-items: center; gap: 12px; margin-bottom: 18px;
    }
    .preview-swatch {
        width: 44px; height: 44px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; transition: background 0.2s;
    }
    .preview-name { font-size: 14px; font-weight: 600; color: #1a1a1a; }
    .preview-sub  { font-size: 11px; color: #bbb; margin-top: 2px; }

    /* ── Submit button ── */
    .btn-create {
        width: 100%; background: #FBCF97; border: none; border-radius: 14px;
        padding: 14px; font-family: 'Syne', sans-serif; font-size: 14px;
        font-weight: 700; color: #1C1C1E; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: all 0.2s;
    }
    .btn-create:hover { background: #f7bc71; transform: translateY(-1px); }
    .btn-create svg { width: 15px; height: 15px; stroke: #1C1C1E; fill: none; stroke-width: 2.5; stroke-linecap: round; }

    /* ── Tip ── */
    .tip-box {
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px;
        padding: 14px 16px; font-size: 12px; color: #065f46;
        margin-top: 16px; display: flex; gap: 10px; line-height: 1.5;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1100px) {
        .layout { grid-template-columns: 1fr; }
        .add-panel { position: static; }
    }
    @media (max-width: 768px) {
        .main-content { margin-left: 0; padding: 24px 20px; }
    }
</style>
@endpush

@section('content')
<main class="main-content">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flash success">✅ {{ session('success') }}</div>
    @endif
    @if($errors->any() && !$errors->has('name') && !$errors->has('color'))
    <div class="flash error">⚠️ Please fix the errors below.</div>
    @endif

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <div class="page-title">My Categories</div>
            <div class="page-sub">Create custom categories to organise your transactions your way</div>
        </div>
    </div>

    <div class="layout">

        {{-- ── Left: category lists ── --}}
        <div style="display:flex;flex-direction:column;gap:22px;">

            {{-- Custom categories --}}
            <div class="panel">
                <div class="panel-title">
                    <span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-2px;margin-right:6px;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        My Custom Categories
                    </span>
                    <span class="panel-count">{{ $customCategories->count() }}</span>
                </div>

                @if($customCategories->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">🏷️</div>
                    <p>You haven't created any custom categories yet.<br>Add your first one using the form →</p>
                </div>
                @else
                <div class="cat-list">
                    @foreach($customCategories as $cat)
                    <div>
                        <div class="cat-row">
                            <div class="cat-swatch" style="background:{{ $cat->color }}22;">
                                <span style="font-size:18px;">🏷️</span>
                            </div>
                            <div class="cat-info">
                                <div class="cat-name">{{ $cat->name }}</div>
                                <div class="cat-meta">Custom · created {{ $cat->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="cat-color-pill">
                                <div class="cat-color-dot" style="background:{{ $cat->color }};"></div>
                                {{ $cat->color }}
                            </div>
                            <div class="cat-actions">
                                <button type="button" class="btn-edit"
                                        onclick="toggleInlineEdit({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->color }}')">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('categories.destroy', $cat->id) }}"
                                      onsubmit="return confirm('Delete \'{{ $cat->name }}\'? Any transactions using it will lose their category.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-del" title="Delete">✕</button>
                                </form>
                            </div>
                        </div>

                        {{-- Inline edit form --}}
                        <div class="inline-edit" id="ie-{{ $cat->id }}">
                            <form method="POST" action="{{ route('categories.update', $cat->id) }}">
                                @csrf @method('PATCH')
                                <div class="ie-row">
                                    <input type="text" name="name" class="ie-input"
                                           id="ie-name-{{ $cat->id }}"
                                           placeholder="Category name" maxlength="28" required>
                                    <div class="ie-color-wrap">
                                        <div class="ie-color-btn" id="ie-swatch-{{ $cat->id }}">
                                            <input type="color" name="color" class="ie-color-input"
                                                   id="ie-color-{{ $cat->id }}"
                                                   oninput="updateIeSwatch({{ $cat->id }}, this.value)">
                                        </div>
                                    </div>
                                </div>
                                <div class="ie-btns">
                                    <button type="submit" class="ie-save">Save Changes</button>
                                    <button type="button" class="ie-cancel"
                                            onclick="toggleInlineEdit({{ $cat->id }})">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- System / default categories (read-only) --}}
            <div class="panel" style="animation-delay:0.15s;">
                <div class="panel-title">
                    <span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2EB872" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-2px;margin-right:6px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Default Categories
                    </span>
                    <span class="panel-count">{{ $systemCategories->count() }}</span>
                </div>
                <p style="font-size:12px;color:#bbb;margin-bottom:16px;">These are available to all users and cannot be edited.</p>
                <div class="sys-grid">
                    @foreach($systemCategories as $cat)
                    <div class="sys-chip">
                        <div class="sys-dot" style="background:{{ $cat->color }};"></div>
                        {{ $cat->name }}
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Right: create form ── --}}
        <div class="add-panel">
            <div class="panel" style="animation-delay:0.05s;">

                <div class="form-section-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Create New Category
                </div>

                {{-- Live preview --}}
                <div class="preview-box" id="previewBox">
                    <div class="preview-swatch" id="previewSwatch" style="background:#FBCF97;">
                        🏷️
                    </div>
                    <div>
                        <div class="preview-name" id="previewName">Category Name</div>
                        <div class="preview-sub">Custom category</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('categories.store') }}" id="createForm">
                    @csrf

                    {{-- Name --}}
                    <div class="form-group">
                        <label class="form-label" for="name">Category Name</label>
                        <input type="text" name="name" id="name"
                               class="form-input {{ $errors->has('name') ? 'border-red-400' : '' }}"
                               placeholder="e.g. Pet Care, Side Hustle…"
                               maxlength="28"
                               value="{{ old('name') }}"
                               oninput="updatePreviewName(this.value)"
                               required>
                        <div class="char-count"><span id="charCount">{{ strlen(old('name', '')) }}</span> / 28</div>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    {{-- Color --}}
                    <div class="form-group">
                        <label class="form-label">Color</label>
                        <div class="color-field">
                            <div class="color-preview-btn" id="colorPreviewBtn" style="background:{{ old('color', '#FBCF97') }};">
                                <input type="color" name="color" id="colorPicker"
                                       class="color-native"
                                       value="{{ old('color', '#FBCF97') }}"
                                       oninput="syncColor(this.value)">
                            </div>
                            <input type="text" id="colorHex" class="color-hex-input"
                                   placeholder="#FBCF97" maxlength="7"
                                   value="{{ old('color', '#FBCF97') }}"
                                   oninput="syncHex(this.value)">
                        </div>
                        @error('color') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    {{-- Palette --}}
                    <div class="form-group">
                        <div class="palette-label">Quick palette</div>
                        <div class="palette" id="palette">
                            @php
                                $palette = [
                                    '#FBCF97','#2EB872','#f87171','#60a5fa',
                                    '#a78bfa','#34d399','#fbbf24','#f472b6',
                                    '#38bdf8','#fb7185','#4ade80','#c084fc',
                                    '#e879f9','#22d3ee','#a3e635','#1C1C1E',
                                ];
                            @endphp
                            @foreach($palette as $color)
                            <div class="swatch {{ old('color', '#FBCF97') === $color ? 'selected' : '' }}"
                                 style="background:{{ $color }};"
                                 data-color="{{ $color }}"
                                 onclick="pickSwatch('{{ $color }}')">
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn-create">
                        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Create Category
                    </button>
                </form>

                <div class="tip-box">
                    <span>💡</span>
                    <span>Custom categories are only visible to you. You can use them everywhere — transactions, budgets, and reports.</span>
                </div>

            </div>
        </div>

    </div>

</main>
@endsection

@push('scripts')
<script>
    // ── Create form: live color sync ──────────────────────────────────────
    function syncColor(val) {
        document.getElementById('colorHex').value = val;
        document.getElementById('colorPreviewBtn').style.background = val;
        updatePreviewSwatch(val);
        highlightSwatch(val);
    }

    function syncHex(val) {
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            document.getElementById('colorPicker').value = val;
            document.getElementById('colorPreviewBtn').style.background = val;
            updatePreviewSwatch(val);
            highlightSwatch(val);
        }
    }

    function pickSwatch(color) {
        document.getElementById('colorPicker').value = color;
        document.getElementById('colorHex').value = color;
        document.getElementById('colorPreviewBtn').style.background = color;
        updatePreviewSwatch(color);
        highlightSwatch(color);
    }

    function highlightSwatch(color) {
        document.querySelectorAll('#palette .swatch').forEach(s => {
            s.classList.toggle('selected', s.dataset.color === color);
        });
    }

    function updatePreviewSwatch(color) {
        document.getElementById('previewSwatch').style.background = color + '33';
    }

    function updatePreviewName(val) {
        document.getElementById('previewName').textContent = val.trim() || 'Category Name';
        document.getElementById('charCount').textContent = val.length;
    }

    // ── Inline edit ───────────────────────────────────────────────────────
    function toggleInlineEdit(id, name, color) {
        const el = document.getElementById('ie-' + id);
        const isOpen = el.classList.contains('open');

        // Close all open inline edits first
        document.querySelectorAll('.inline-edit.open').forEach(e => e.classList.remove('open'));

        if (!isOpen) {
            el.classList.add('open');
            const nameInput  = document.getElementById('ie-name-' + id);
            const colorInput = document.getElementById('ie-color-' + id);
            const swatch     = document.getElementById('ie-swatch-' + id);

            if (name  !== undefined) nameInput.value  = name;
            if (color !== undefined) { colorInput.value = color; swatch.style.background = color; }

            nameInput.focus();
        }
    }

    function updateIeSwatch(id, color) {
        document.getElementById('ie-swatch-' + id).style.background = color;
    }

    // ── Keyboard shortcuts ────────────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.inline-edit.open').forEach(el => el.classList.remove('open'));
        }
    });
</script>
@endpush