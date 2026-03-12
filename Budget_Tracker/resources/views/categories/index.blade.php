@extends('layouts.app')

@section('title', 'My Categories')

@push('styles')
    @vite('resources/css/categories.css')
@endpush

@section('content')
    <main class="main-content">

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="flash success">✅ {{ session('success') }}</div>
        @endif
        @if ($errors->any() && !$errors->has('name') && !$errors->has('color'))
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
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                style="display:inline;vertical-align:-2px;margin-right:6px;">
                                <path
                                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                            My Custom Categories
                        </span>
                        <span class="panel-count">{{ $customCategories->count() }}</span>
                    </div>

                    @if ($customCategories->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">🏷️</div>
                            <p>You haven't created any custom categories yet.<br>Add your first one using the form →</p>
                        </div>
                    @else
                        <div class="cat-list">
                            @foreach ($customCategories as $cat)
                                <div>
                                    <div class="cat-row">
                                        <div class="cat-swatch" style="background:{{ $cat->color }}22;">
                                            <span style="font-size:18px;">🏷️</span>
                                        </div>
                                        <div class="cat-info">
                                            <div class="cat-name">{{ $cat->name }}</div>
                                            <div class="cat-meta">Custom · created {{ $cat->created_at->diffForHumans() }}
                                            </div>
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
                                                    id="ie-name-{{ $cat->id }}" placeholder="Category name"
                                                    maxlength="28" required>
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
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2EB872"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                style="display:inline;vertical-align:-2px;margin-right:6px;">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            Default Categories
                        </span>
                        <span class="panel-count">{{ $systemCategories->count() }}</span>
                    </div>
                    <p style="font-size:12px;color:#bbb;margin-bottom:16px;">These are available to all users and cannot be
                        edited.</p>
                    <div class="sys-grid">
                        @foreach ($systemCategories as $cat)
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
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FBCF97"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
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
                                placeholder="e.g. Pet Care, Side Hustle…" maxlength="28" value="{{ old('name') }}"
                                oninput="updatePreviewName(this.value)" required>
                            <div class="char-count"><span id="charCount">{{ strlen(old('name', '')) }}</span> / 28</div>
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Color --}}
                        <div class="form-group">
                            <label class="form-label">Color</label>
                            <div class="color-field">
                                <div class="color-preview-btn" id="colorPreviewBtn"
                                    style="background:{{ old('color', '#FBCF97') }};">
                                    <input type="color" name="color" id="colorPicker" class="color-native"
                                        value="{{ old('color', '#FBCF97') }}" oninput="syncColor(this.value)">
                                </div>
                                <input type="text" id="colorHex" class="color-hex-input" placeholder="#FBCF97"
                                    maxlength="7" value="{{ old('color', '#FBCF97') }}" oninput="syncHex(this.value)">
                            </div>
                            @error('color')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Palette --}}
                        <div class="form-group">
                            <div class="palette-label">Quick palette</div>
                            <div class="palette" id="palette">
                                @php
                                    $palette = [
                                        '#FBCF97',
                                        '#2EB872',
                                        '#f87171',
                                        '#60a5fa',
                                        '#a78bfa',
                                        '#34d399',
                                        '#fbbf24',
                                        '#f472b6',
                                        '#38bdf8',
                                        '#fb7185',
                                        '#4ade80',
                                        '#c084fc',
                                        '#e879f9',
                                        '#22d3ee',
                                        '#a3e635',
                                        '#1C1C1E',
                                    ];
                                @endphp
                                @foreach ($palette as $color)
                                    <div class="swatch {{ old('color', '#FBCF97') === $color ? 'selected' : '' }}"
                                        style="background:{{ $color }};" data-color="{{ $color }}"
                                        onclick="pickSwatch('{{ $color }}')">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn-create">
                            <svg viewBox="0 0 24 24">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Create Category
                        </button>
                    </form>

                    <div class="tip-box">
                        <span>💡</span>
                        <span>Custom categories are only visible to you. You can use them everywhere — transactions,
                            budgets, and reports.</span>
                    </div>

                </div>
            </div>

        </div>

    </main>
@endsection

@push('scripts')
    @vite('resources/js/categories.js')
@endpush
