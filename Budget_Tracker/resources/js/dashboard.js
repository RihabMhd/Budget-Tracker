// ── Modal helpers ──────────────────────────────────────────────
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('hidden');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('hidden');
}

// ── Boot ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

    // Open modal  [data-open-modal="modal-id"]
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.dataset.openModal));
    });

    // Close modal buttons  [data-close-modal="modal-id"]
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
            document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(m => {
                closeModal(m.id);
            });
        }
    });

    // Re-open modal if validation errors exist (form was submitted but failed)
    const reopenTrigger = document.querySelector('[data-reopen-modal]');
    if (reopenTrigger) {
        openModal(reopenTrigger.dataset.reopenModal);
    }
});