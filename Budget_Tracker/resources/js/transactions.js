// ── Modal helpers ──────────────────────────────────────────────
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('hidden');
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}

// ── Category dropdown helpers ──────────────────────────────────
function selectCat(target, id, name, color, dashed) {
    const dot   = document.getElementById(`${target}-cat-dot`);
    const label = document.getElementById(`${target}-cat-label`);
    const input = document.getElementById(`${target}-cat-input`);

    if (dot) {
        dot.style.background = dashed ? 'transparent' : color;
        dot.style.border     = dashed ? '1.5px dashed #ccc' : 'none';
    }
    if (label) label.textContent = name;
    if (input) input.value = id;

    // Close dropdown
    const dropdown = document.getElementById(`${target}-cat-dropdown`);
    if (dropdown) dropdown.classList.add('hidden');
}

// ── Boot ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

    // Open modal  [data-open-modal="modal-id"]
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.dataset.openModal));
    });

    // Close modal  [data-close-modal="modal-id"]
    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', () => closeModal(btn.dataset.closeModal));
    });

    // Click overlay to dismiss  [data-dismiss-modal="modal-id"]
    document.querySelectorAll('[data-dismiss-modal]').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) closeModal(overlay.dataset.dismissModal);
        });
    });

    // Escape key closes any open modal
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(m => closeModal(m.id));
        }
    });

    // Re-open modal on validation error
    const reopenTrigger = document.querySelector('[data-reopen-modal]');
    if (reopenTrigger) openModal(reopenTrigger.dataset.reopenModal);

    // Category dropdown toggles  [data-toggle-dropdown="dropdown-id"]
    document.querySelectorAll('[data-toggle-dropdown]').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const dropdown = document.getElementById(trigger.dataset.toggleDropdown);
            if (dropdown) dropdown.classList.toggle('hidden');
        });
    });

    // Category option selection  [data-cat-target data-cat-id data-cat-name data-cat-color data-cat-dashed]
    document.querySelectorAll('.cat-option[data-cat-target]').forEach(option => {
        option.addEventListener('click', () => {
            selectCat(
                option.dataset.catTarget,
                option.dataset.catId,
                option.dataset.catName,
                option.dataset.catColor,
                option.dataset.catDashed === 'true'
            );
        });
    });

    // Close category dropdowns when clicking outside
    document.addEventListener('click', e => {
        document.querySelectorAll('.cat-select-dropdown:not(.hidden)').forEach(dropdown => {
            const wrap = dropdown.closest('.cat-select-wrap');
            if (wrap && !wrap.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });

    // Delete form submit via button  [data-submit-form="form-id"]
    document.querySelectorAll('[data-submit-form]').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = document.getElementById(btn.dataset.submitForm);
            if (form && confirm('Permanently delete this expense?')) {
                form.submit();
            }
        });
    });
});