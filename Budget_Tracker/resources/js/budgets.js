window.toggleEdit = function (catId) {
    const el = document.getElementById('edit-' + catId);

    if (el.style.display === 'none' || el.style.display === '') {
        el.style.display = 'block';
        el.querySelector('input[type="number"]').focus();
    } else {
        el.style.display = 'none';
    }
};