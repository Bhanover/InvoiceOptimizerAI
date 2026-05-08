// js/cliente.js
// Responsabilidad: interactividad exclusiva de pages/cliente.php

// ── Pestañas ──────────────────────────────────────────────
function switchTab(tab) {
    // Actualizar URL sin recargar
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.pushState({}, '', url);

    // Mostrar panel correcto
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    document.querySelector('[data-tab="' + tab + '"]').classList.add('active');
}

// ── Modal cambio de estado de factura ─────────────────────
function abrirModalEstado(facturaId, estadoActual) {
    document.getElementById('modal-estado-factura-id').value = facturaId;
    document.getElementById('modal-estado-select').value = estadoActual;
    document.getElementById('modal-estado-cliente').classList.add('open');
}

function cerrarModalEstado() {
    document.getElementById('modal-estado-cliente').classList.remove('open');
}

document.getElementById('modal-estado-cliente')?.addEventListener('click', e => {
    if (e.target === e.currentTarget) cerrarModalEstado();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') cerrarModalEstado();
});
// Activar la tab correcta al cargar la página
const tabActual = new URL(window.location).searchParams.get('tab') || 'resumen';
switchTab(tabActual);