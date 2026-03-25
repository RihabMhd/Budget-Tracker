window.syncColor = function(val) {
    document.getElementById('colorHex').value = val;
    document.getElementById('colorPreviewBtn').style.background = val;
    updatePreviewSwatch(val);
    highlightSwatch(val);
}

window.syncHex = function(val) {
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
        document.getElementById('colorPicker').value = val;
        document.getElementById('colorPreviewBtn').style.background = val;
        updatePreviewSwatch(val);
        highlightSwatch(val);
    }
}

window.pickSwatch = function(color) {
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
    const preview = document.getElementById('previewSwatch');
    if (preview) {
        preview.style.background = color + '33';
    }
}

window.updatePreviewName = function(val) {
    const nameEl = document.getElementById('previewName');
    const countEl = document.getElementById('charCount');
    if (nameEl) nameEl.textContent = val.trim() || 'Category Name';
    if (countEl) countEl.textContent = val.length;
}

window.toggleInlineEdit = function(id, name, color) {
    const el = document.getElementById('ie-' + id);
    const isOpen = el.classList.contains('open');

   
    document.querySelectorAll('.inline-edit.open').forEach(e => e.classList.remove('open'));

    if (!isOpen) {
        el.classList.add('open');
        const nameInput = document.getElementById('ie-name-' + id);
        const colorInput = document.getElementById('ie-color-' + id);
        const swatch = document.getElementById('ie-swatch-' + id);

        if (name !== undefined) nameInput.value = name;
        if (color !== undefined) { 
            colorInput.value = color; 
            swatch.style.background = color; 
        }

        nameInput.focus();
    }
}

window.updateIeSwatch = function(id, color) {
    document.getElementById('ie-swatch-' + id).style.background = color;
}


document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.inline-edit.open').forEach(el => el.classList.remove('open'));
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('name');
    const colorInput = document.getElementById('colorPicker');

    if (nameInput && nameInput.value) {
        window.updatePreviewName(nameInput.value);
    }

    if (colorInput && colorInput.value) {
        highlightSwatch(colorInput.value);
        updatePreviewSwatch(colorInput.value);
    }
});