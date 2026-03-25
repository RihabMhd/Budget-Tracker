function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal('add-modal'); });

function setType(val) {
    document.getElementById('type-input').value = val;
    document.getElementById('opt-expense').className = 'type-opt' + (val === 'Expense' ? ' active-expense' : '');
    document.getElementById('opt-income').className = 'type-opt' + (val === 'Income' ? ' active-income' : '');
}