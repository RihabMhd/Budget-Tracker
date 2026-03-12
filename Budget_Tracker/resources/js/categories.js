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
        const nameInput = document.getElementById('ie-name-' + id);
        const colorInput = document.getElementById('ie-color-' + id);
        const swatch = document.getElementById('ie-swatch-' + id);

        if (name !== undefined) nameInput.value = name;
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