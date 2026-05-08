/**
 * InvoiceOptimizer Ai — Tabla de revisión de ofertas importadas desde PDF
 * Depende de: ofertas.js (ofertasImportadas)
 * Estilos: ofertas-revision.css
 */
 
/* ───────────────── HELPERS ───────────────── */
 
const fmt6 = v => {
    const n = parseFloat(v);
    return isNaN(n) ? '0' : parseFloat(n.toFixed(6)).toString();
};
 
const inp = (name, value, modifier) =>
    `<input name="${name}" value="${value}"
        class="rev-input rev-input--${modifier}"
        onclick="event.stopPropagation()">`;
 
 
/* ───────────────── RENDER ───────────────── */
 
function mostrarRevision(ofertas) {
    const tabla = document.getElementById('tabla-revision');
    if (!tabla) return;

    // ── Detectar qué columnas tienen datos ──
    const cols = {
        energia_p1:  ofertas.some(o => parseFloat(o.precio_energia_p1)  > 0),
        energia_p2:  ofertas.some(o => parseFloat(o.precio_energia_p2)  > 0),
        energia_p3:  ofertas.some(o => parseFloat(o.precio_energia_p3)  > 0),
        energia_p4:  ofertas.some(o => parseFloat(o.precio_energia_p4)  > 0),
        energia_p5:  ofertas.some(o => parseFloat(o.precio_energia_p5)  > 0),
        energia_p6:  ofertas.some(o => parseFloat(o.precio_energia_p6)  > 0),
        potencia_p1: ofertas.some(o => parseFloat(o.precio_potencia_p1) > 0),
        potencia_p2: ofertas.some(o => parseFloat(o.precio_potencia_p2) > 0),
        potencia_p3: ofertas.some(o => parseFloat(o.precio_potencia_p3) > 0),
        potencia_p4: ofertas.some(o => parseFloat(o.precio_potencia_p4) > 0),
        potencia_p5: ofertas.some(o => parseFloat(o.precio_potencia_p5) > 0),
        potencia_p6: ofertas.some(o => parseFloat(o.precio_potencia_p6) > 0),
        fee_min:     ofertas.some(o => parseFloat(o.fee_minimo)         > 0),
        fee_max:     ofertas.some(o => parseFloat(o.fee_maximo)         > 0),
    };

    // ── Cabecera dinámica ──
    const headers = [
        '<div>Nombre</div>',
        '<div>Comercializadora</div>',
        '<div>Versión</div>',
        '<div>Tipo</div>',
        cols.energia_p1  ? '<div>En.P1</div>'  : '',
        cols.energia_p2  ? '<div>En.P2</div>'  : '',
        cols.energia_p3  ? '<div>En.P3</div>'  : '',
        cols.energia_p4  ? '<div>En.P4</div>'  : '',
        cols.energia_p5  ? '<div>En.P5</div>'  : '',
        cols.energia_p6  ? '<div>En.P6</div>'  : '',
        cols.potencia_p1 ? '<div>Pot.P1</div>' : '',
        cols.potencia_p2 ? '<div>Pot.P2</div>' : '',
        cols.potencia_p3 ? '<div>Pot.P3</div>' : '',
        cols.potencia_p4 ? '<div>Pot.P4</div>' : '',
        cols.potencia_p5 ? '<div>Pot.P5</div>' : '',
        cols.potencia_p6 ? '<div>Pot.P6</div>' : '',
        cols.fee_min     ? '<div>Fee min</div>' : '',
        cols.fee_max     ? '<div>Fee max</div>' : '',
    ].join('');

    // ── Filas dinámicas ──
    const rows = ofertas.map((o, idx) => {
        const celdas = [
            `<div>${inp('nombre_oferta',    o.nombre_oferta,    'bold')}</div>`,
            `<div>${inp('comercializadora', o.comercializadora, 'muted')}</div>`,
            `<div>${inp('version_tarifa',   o.version_tarifa,   'muted')}</div>`,
            `<div>${inp('tipo_tarifa',      o.tipo_tarifa,      'muted')}</div>`,
            cols.energia_p1  ? `<div>${inp('precio_energia_p1',  fmt6(o.precio_energia_p1),  'cyan')}</div>`  : '',
            cols.energia_p2  ? `<div>${inp('precio_energia_p2',  fmt6(o.precio_energia_p2),  'cyan')}</div>`  : '',
            cols.energia_p3  ? `<div>${inp('precio_energia_p3',  fmt6(o.precio_energia_p3),  'cyan')}</div>`  : '',
            cols.energia_p4  ? `<div>${inp('precio_energia_p4',  fmt6(o.precio_energia_p4),  'cyan')}</div>`  : '',
            cols.energia_p5  ? `<div>${inp('precio_energia_p5',  fmt6(o.precio_energia_p5),  'cyan')}</div>`  : '',
            cols.energia_p6  ? `<div>${inp('precio_energia_p6',  fmt6(o.precio_energia_p6),  'cyan')}</div>`  : '',
            cols.potencia_p1 ? `<div>${inp('precio_potencia_p1', fmt6(o.precio_potencia_p1), 'cyan')}</div>` : '',
            cols.potencia_p2 ? `<div>${inp('precio_potencia_p2', fmt6(o.precio_potencia_p2), 'cyan')}</div>` : '',
            cols.potencia_p3 ? `<div>${inp('precio_potencia_p3', fmt6(o.precio_potencia_p3), 'cyan')}</div>` : '',
            cols.potencia_p4 ? `<div>${inp('precio_potencia_p4', fmt6(o.precio_potencia_p4), 'cyan')}</div>` : '',
            cols.potencia_p5 ? `<div>${inp('precio_potencia_p5', fmt6(o.precio_potencia_p5), 'cyan')}</div>` : '',
            cols.potencia_p6 ? `<div>${inp('precio_potencia_p6', fmt6(o.precio_potencia_p6), 'cyan')}</div>` : '',
            cols.fee_min     ? `<div>${inp('fee_minimo', fmt6(o.fee_minimo), 'amber')}</div>`                : '',
            cols.fee_max     ? `<div>${inp('fee_maximo', fmt6(o.fee_maximo), 'amber')}</div>`               : '',
            `<div style="display:none">${inp('email_comercializadora', o.email_comercializadora || '', 'muted')}</div>`,

        ].join('');

        return `<div class="rev-item" data-idx="${idx}">${celdas}</div>`;
    }).join('');

    // Número de columnas activas para el grid
    const numCols = 4 + Object.values(cols).filter(Boolean).length;
    tabla.innerHTML = `
        <div class="rev-table" style="--rev-cols:${numCols}">
            <div class="rev-header">${headers}</div>
            ${rows}
        </div>`;

    document.getElementById('modal-revision')?.classList.add('open');
}
 
 // Cierra el modal SIN borrar datos (botón ✕ y cancelar)
function cancelarImportacion() {
    document.getElementById('modal-revision')?.classList.remove('open');
}

// Reset completo: solo al procesar un nuevo PDF o al guardar con éxito
function resetImportacion() {
    ofertasImportadas = [];
    document.getElementById('btn-ver-tabla')?.style.setProperty('display', 'none');
    document.getElementById('tabla-revision').innerHTML = '';
}
 
/* ───────────────── RECOGER DATOS ───────────────── */
 
function recogerOfertasEditadas() {
    return [...document.querySelectorAll('#tabla-revision .rev-item')].map(row => {
        const g  = n => row.querySelector(`[name="${n}"]`)?.value?.trim() || '';
        const gf = n => parseFloat(g(n).replace(',', '.')) || 0;

        return {
            nombre_oferta:          g('nombre_oferta'),
            comercializadora:       g('comercializadora'),
            email_comercializadora: g('email_comercializadora'), // ← viene del JSON de n8n
            version_tarifa:         g('version_tarifa'),
            tipo_tarifa:            g('tipo_tarifa'),
            precio_energia_p1:      gf('precio_energia_p1'),
            precio_energia_p2:      gf('precio_energia_p2'),
            precio_energia_p3:      gf('precio_energia_p3'),
            precio_energia_p4:      gf('precio_energia_p4'),
            precio_energia_p5:      gf('precio_energia_p5'),
            precio_energia_p6:      gf('precio_energia_p6'),
            precio_potencia_p1:     gf('precio_potencia_p1'),
            precio_potencia_p2:     gf('precio_potencia_p2'),
            precio_potencia_p3:     gf('precio_potencia_p3'),
            precio_potencia_p4:     gf('precio_potencia_p4'),
            precio_potencia_p5:     gf('precio_potencia_p5'),
            precio_potencia_p6:     gf('precio_potencia_p6'),
            fee_minimo:             gf('fee_minimo'),
            fee_maximo:             gf('fee_maximo'),
            activa: 1
        };
    });
}
 
 
/* ───────────────── SUBMIT ───────────────── */
document.getElementById('form-importar')?.addEventListener('submit', () => {
    document.getElementById('ofertas-json-input').value = 
        JSON.stringify(recogerOfertasEditadas());
});

