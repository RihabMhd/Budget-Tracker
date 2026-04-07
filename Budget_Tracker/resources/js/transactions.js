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
    const dot = document.getElementById(`${target}-cat-dot`);
    const label = document.getElementById(`${target}-cat-label`);
    const input = document.getElementById(`${target}-cat-input`);

    if (dot) {
        dot.style.background = dashed ? 'transparent' : color;
        dot.style.border = dashed ? '1.5px dashed #ccc' : 'none';
    }
    if (label) label.textContent = name;
    if (input) input.value = id;

    const dropdown = document.getElementById(`${target}-cat-dropdown`);
    if (dropdown) dropdown.classList.add('hidden');
}

// ── Boot ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

    // Standard UI Listeners
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.dataset.openModal));
    });

    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', () => closeModal(btn.dataset.closeModal));
    });

    document.querySelectorAll('[data-dismiss-modal]').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) closeModal(overlay.dataset.dismissModal);
        });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(m => closeModal(m.id));
        }
    });

    const reopenTrigger = document.querySelector('[data-reopen-modal]');
    if (reopenTrigger) openModal(reopenTrigger.dataset.reopenModal);

    document.querySelectorAll('[data-toggle-dropdown]').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const dropdown = document.getElementById(trigger.dataset.toggleDropdown);
            if (dropdown) dropdown.classList.toggle('hidden');
        });
    });

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

    document.addEventListener('click', e => {
        document.querySelectorAll('.cat-select-dropdown:not(.hidden)').forEach(dropdown => {
            const wrap = dropdown.closest('.cat-select-wrap');
            if (wrap && !wrap.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });

    document.querySelectorAll('[data-submit-form]').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = document.getElementById(btn.dataset.submitForm);
            if (form && confirm('Permanently delete this expense?')) {
                form.submit();
            }
        });
    });

    // ── Optimized OCR Logic ──────────────────────────────────────
    const scanInput = document.getElementById('receipt-scan-input');
    if (scanInput) {
        scanInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const status = document.getElementById('ocr-status');
            const amountInput = document.querySelector('input[name="amount"]');
            const dateInput = document.querySelector('input[name="date"]');
            const descInput = document.querySelector('input[name="description"]');

            if (status) status.classList.remove('hidden');

            Tesseract.recognize(file, 'eng', {
                logger: m => console.log(m)
            }).then(({ data: { text } }) => {
                if (status) status.classList.add('hidden');

                // Split text into lines to analyze them one by one
                const lines = text.split('\n').map(l => l.trim().toLowerCase());
                console.log("Analyzed Lines:", lines);

                let detectedAmount = null;

                // 1. SEARCH FOR KEYWORDS (The most reliable way)
                // We look for any line containing "total", "payé", "net", or "dh"
                const keywords = ['total', 'net', 'payer', 'payé', 'dh', 'mad'];

                for (let line of lines) {
                    if (keywords.some(key => line.includes(key))) {
                        // Find numbers like 780.00 or 780,00 in this specific line
                        const match = line.match(/(\d+[\.,]\d{2})/);
                        if (match) {
                            detectedAmount = match[1];
                            // If it's a 'total' line, we stop searching - we found it!
                            if (line.includes('total') || line.includes('net')) break;
                        }
                    }
                }

                // 2. FALLBACK: If keywords failed, find the largest number in the whole text
                if (!detectedAmount) {
                    const allNumbers = text.match(/(\d+[\.,]\d{2})/g);
                    if (allNumbers) {
                        // Convert to numbers and find the maximum
                        const nums = allNumbers.map(n => parseFloat(n.replace(',', '.')));
                        detectedAmount = Math.max(...nums).toString();
                    }
                }

                // Apply the result
                if (detectedAmount && amountInput) {
                    amountInput.value = detectedAmount.replace(',', '.');
                    amountInput.classList.add('ocr-highlight'); // Trigger the green glow
                }

                const dateRegex = /(\d{2}[\/.-]\d{2}[\/.-]\d{4})|(\d{4}[\/.-]\d{2}[\/.-]\d{2})/;
                const dateMatch = text.match(dateRegex);
                if (dateMatch && dateInput) {
                    let d = dateMatch[0].replace(/\//g, '-');
                    if (d.match(/^\d{2}-\d{2}-\d{4}$/)) {
                        const p = d.split('-');
                        d = `${p[2]}-${p[1]}-${p[0]}`;
                    }
                    dateInput.value = d;
                }

                const shopLine = lines.find(l => l.length > 5 && !l.includes('2026'));
                if (shopLine && descInput && descInput.value === "") {
                    descInput.value = shopLine.toUpperCase();
                }

            }).catch(err => {
                console.error(err);
                if (status) status.innerText = "Scan failed.";
            });
        });
    }
});