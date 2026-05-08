/**
 * InvoiceOptimizer Ai — Avisos JS (simplificado)
 */

// ── Modal handling ─────────────────────────────
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.remove('open');
    document.body.style.overflow = 'auto';
}

// ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(modal => {
            modal.classList.remove('open');
        });
        document.body.style.overflow = 'auto';
    }
});
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('recalcularForm');
    const btn = document.getElementById('btnRecalcular');

    if (!form || !btn) return;

    form.addEventListener('submit', () => {
        btn.disabled = true;
        btn.innerHTML = '⏳ Recalculando...';
    });
});

// Click fuera del modal (en el overlay)
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        closeModal(e.target.id);
    }
});