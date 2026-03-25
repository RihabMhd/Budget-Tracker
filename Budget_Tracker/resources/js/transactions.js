/**
 * Transaction Management JS
 * Handles Modals, Type Selection, and Custom Category Dropdowns
 */

document.addEventListener('DOMContentLoaded', function () {

    // --- 1. Modal Logic ---
    
    window.openModal = function (id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            // Prevent background scrolling when modal is active
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = function (id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            // Restore scrolling
            document.body.style.overflow = '';
        }
    };

    // --- 2. Transaction Type Toggle (Income/Expense) ---

    window.setType = function (val) {
        const input = document.getElementById('type-input');
        const expOpt = document.getElementById('opt-expense');
        const incOpt = document.getElementById('opt-income');

        if (input) {
            input.value = val;
        }

        // Update UI states based on selection
        if (expOpt && incOpt) {
            expOpt.className = 'type-opt' + (val === 'Expense' ? ' active-expense' : '');
            incOpt.className = 'type-opt' + (val === 'Income' ? ' active-income' : '');
        }
    };

    // --- 3. Custom Category Select Dropdown ---

    window.toggleCatDropdown = function (prefix) {
        const dropdown = document.getElementById(prefix + '-cat-dropdown');
        const trigger = document.getElementById(prefix + '-cat-trigger');
        
        if (!dropdown || !trigger) return;

        const isOpen = !dropdown.classList.contains('hidden');

        // Close all other open dropdowns first to avoid overlaps
        document.querySelectorAll('.cat-select-dropdown').forEach(d => d.classList.add('hidden'));
        document.querySelectorAll('.cat-select-trigger').forEach(t => t.classList.remove('open'));

        if (!isOpen) {
            dropdown.classList.remove('hidden');
            trigger.classList.add('open');
        }
    };

    window.selectCat = function (prefix, id, name, color, isDashed) {
        const input = document.getElementById(prefix + '-cat-input');
        const label = document.getElementById(prefix + '-cat-label');
        const dot = document.getElementById(prefix + '-cat-dot');
        const dropdown = document.getElementById(prefix + '-cat-dropdown');
        const trigger = document.getElementById(prefix + '-cat-trigger');

        // Set hidden input value for the form
        if (input) input.value = id;
        
        // Update trigger UI
        if (label) label.textContent = name;
        
        if (dot) {
            dot.style.background = isDashed ? 'transparent' : color;
            dot.style.border = isDashed ? '1.5px dashed #ccc' : 'none';
        }

        // Close dropdown
        if (dropdown) dropdown.classList.add('hidden');
        if (trigger) trigger.classList.remove('open');
    };

    // --- 4. Global Click Listener (Close dropdowns when clicking outside) ---

    document.addEventListener('click', function (e) {
        // If the click is not inside the category wrapper, close all dropdowns
        if (!e.target.closest('.cat-select-wrap')) {
            document.querySelectorAll('.cat-select-dropdown').forEach(d => {
                d.classList.add('hidden');
            });
            document.querySelectorAll('.cat-select-trigger').forEach(t => {
                t.classList.remove('open');
            });
        }
    });

    // --- 5. Image Preview (Optional Improvement) ---
    // If you have an input with id "receipt_image", this shows a preview
    const imageInput = document.querySelector('input[name="receipt_image"]');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                console.log("File selected: " + this.files[0].name);
                // You can add logic here to show a thumbnail preview in the modal
            }
        });
    }
});