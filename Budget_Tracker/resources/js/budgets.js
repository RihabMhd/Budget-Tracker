function toggleEdit(catId) {
    const el = document.getElementById('edit-' + catId);
    el.classList.toggle('open');
    if (el.classList.contains('open')) {
        el.querySelector('input[type="number"]').focus();
    }
}