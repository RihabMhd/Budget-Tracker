function setType(val) {
    document.getElementById('type-input').value = val;
    document.getElementById('opt-expense').className = 'type-opt' + (val === 'Expense' ? ' active-expense' : '');
    document.getElementById('opt-income').className = 'type-opt' + (val === 'Income' ? ' active-income' : '');
}
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function setType(val) {
    document.getElementById('type-input').value = val;
    const expOpt = document.getElementById('opt-expense');
    const incOpt = document.getElementById('opt-income');
    expOpt.className = 'type-opt' + (val === 'Expense' ? ' active-expense' : '');
    incOpt.className = 'type-opt' + (val === 'Income' ? ' active-income' : '');
}

// ── Custom category select ──
function toggleCatDropdown(prefix) {
    const dropdown = document.getElementById(prefix + '-cat-dropdown');
    const trigger = document.getElementById(prefix + '-cat-trigger');
    const isOpen = !dropdown.classList.contains('hidden');
    // Close all open dropdowns first
    document.querySelectorAll('.cat-select-dropdown').forEach(d => d.classList.add('hidden'));
    document.querySelectorAll('.cat-select-trigger').forEach(t => t.classList.remove('open'));
    if (!isOpen) {
        dropdown.classList.remove('hidden');
        trigger.classList.add('open');
    }
}

function selectCat(prefix, id, name, color, isDashed) {
    document.getElementById(prefix + '-cat-input').value = id;
    document.getElementById(prefix + '-cat-label').textContent = name;
    const dot = document.getElementById(prefix + '-cat-dot');
    dot.style.background = isDashed ? 'transparent' : color;
    dot.style.border = isDashed ? '1.5px dashed #ccc' : 'none';
    document.getElementById(prefix + '-cat-dropdown').classList.add('hidden');
    document.getElementById(prefix + '-cat-trigger').classList.remove('open');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function (e) {
    if (!e.target.closest('.cat-select-wrap')) {
        document.querySelectorAll('.cat-select-dropdown').forEach(d => d.classList.add('hidden'));
        document.querySelectorAll('.cat-select-trigger').forEach(t => t.classList.remove('open'));
    }
});