/**
 * InvoiceOptimizer Ai — Modales, menú y PDF upload
 */
 
let ofertasImportadas = [];
let _procesando = false;   // bloquea cierre de modal durante la carga
 
/* ───────────────── MODALES ───────────────── */
 
const abrirModal = id => {
    document.getElementById(id)?.classList.add('open');
};
 
const cerrarModales = () => {
    // No cerrar si hay una carga en curso
    if (_procesando) return;
    document.querySelectorAll('.modal-overlay.open')
        .forEach(m => m.classList.remove('open'));
};
 
const abrirModalCrear = () => abrirModal('modal-crear');
 
function abrirModalEditar(oferta, e) {
    e?.stopPropagation();
 
    const f = document.getElementById('form-editar');
    if (!f) return;
 
    const set = (n, v) => {
        const el = f.querySelector(`[name="${n}"]`);
        if (el) el.value = v ?? '';
    };
 
    set('oferta_id',              oferta.id);
    set('nombre_oferta',          oferta.nombre_oferta);
    set('comercializadora',       oferta.comercializadora);
    set('email_comercializadora', oferta.email_comercializadora);
    set('version_tarifa',         oferta.version_tarifa);
    set('tipo_tarifa',            oferta.tipo_tarifa);
    set('fee_minimo',             oferta.fee_minimo);
    set('fee_maximo',             oferta.fee_maximo);
 
    f.querySelector('[name="activa"]').checked = !!oferta.activa;
 
    for (let i = 1; i <= 6; i++) {
        set(`precio_energia_p${i}`,  oferta[`precio_energia_p${i}`]);
        set(`precio_potencia_p${i}`, oferta[`precio_potencia_p${i}`]);
    }
 
    abrirModal('modal-editar');
}
 
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { cerrarModales(); cerrarMenus(); }
});
 
document.addEventListener('click', e => {
    const modal = document.querySelector('.modal-overlay.open');
    if (modal && e.target === modal) cerrarModales();
});
 
 
/* ───────────────── MENÚ ⋮ ───────────────── */
 
const cerrarMenus = () => {
    document.querySelectorAll('.menu-opciones.open')
        .forEach(m => m.classList.remove('open'));
};
 
function abrirMenuOpciones(btn, e) {
    e?.stopPropagation();
    const menu = btn.nextElementSibling;
    if (!menu) return;
    const abierto = menu.classList.contains('open');
    cerrarMenus();
    if (!abierto) menu.classList.add('open');
}
 
document.addEventListener('click', e => {
    if (!e.target.closest('.oferta-menu-wrap')) cerrarMenus();
});
 
 
/* ───────────────── LOADER ───────────────── */

const FASES = [
    { texto: 'Subiendo PDF al servidor...',       duracion: 2000  },
    { texto: 'Extrayendo texto del documento...', duracion: 5000  },
    { texto: 'Analizando tarifas con IA...',      duracion: 35000 },
    { texto: 'Procesando y validando datos...',   duracion: 10000 },
    { texto: 'Preparando tabla de revisión...',   duracion: 3000  },
];

let _loaderIntervals = [];  // FIX: array en lugar de variable única
let _loaderTimeouts  = [];  // FIX: array en lugar de variable única
let _faseActual      = 0;
let _progreso        = 0;
let _progresoTarget  = 0;

function _resetLoaderDOM() {
    // FIX: limpia restos visuales de errores anteriores
    const texto = document.getElementById('loader-texto');
    const barra = document.getElementById('loader-barra');
    const modal  = document.getElementById('modal-loader');

    if (texto) {
        texto.textContent = '';
        texto.style.color = '';          // quita el rojo si quedó de un error
        texto.classList.remove('loader-fade-in');
    }
    if (barra) {
        barra.style.width = '0%';
        barra.style.background = '';    // quita el rojo si quedó de un error
    }
    document.querySelectorAll('.loader-dot')
        .forEach(d => d.classList.remove('active'));
}

function _cancelarTimers() {
    _loaderIntervals.forEach(clearInterval);
    _loaderTimeouts.forEach(clearTimeout);
    _loaderIntervals = [];
    _loaderTimeouts  = [];
}

function iniciarLoader() {
    _cancelarTimers();       // FIX: cancela timers previos antes de crear nuevos
    _resetLoaderDOM();       // FIX: limpia estado visual anterior

    _faseActual     = 0;
    _progreso       = 0;
    _progresoTarget = 0;

    const modal = document.getElementById('modal-loader');
    if (!modal) return;

    _actualizarFase(0);
    modal.classList.add('open');

    // Fases programadas
    let acumulado = 0;
    FASES.forEach((fase, idx) => {
        if (idx === 0) return;
        acumulado += FASES[idx - 1].duracion;
        // FIX: guardar cada timeout en el array
        _loaderTimeouts.push(setTimeout(() => _actualizarFase(idx), acumulado));
    });

    // Barra de progreso suave
    _loaderIntervals.push(setInterval(() => {
        if (_progreso < _progresoTarget) {
            _progreso = Math.min(_progreso + 0.4, _progresoTarget);
            _setBarra(_progreso);
        }
    }, 100));
}

function _actualizarFase(idx) {
    _faseActual = idx;
    const fase  = FASES[idx];

    const el = document.getElementById('loader-texto');
    if (el) {
        el.classList.remove('loader-fade-in');
        void el.offsetWidth;
        el.textContent = fase.texto;
        el.classList.add('loader-fade-in');
    }

    document.querySelectorAll('.loader-dot').forEach((d, i) => {
        d.classList.toggle('active', i <= idx);
    });

    const totalDuracion   = FASES.reduce((s, f) => s + f.duracion, 0);
    const duracionHasta   = FASES.slice(0, idx + 1).reduce((s, f) => s + f.duracion, 0);
    _progresoTarget = Math.min((duracionHasta / totalDuracion) * 92, 92);
}

function _setBarra(pct) {
    const barra = document.getElementById('loader-barra');
    if (barra) barra.style.width = pct + '%';
}

function finalizarLoader(ok) {
    _cancelarTimers();

    _progresoTarget = 100;
    _progreso       = 100;
    _setBarra(100);

    // FIX: en error esperamos más tiempo para que el usuario lea el mensaje
    const delay = ok ? 400 : 2800;
    _loaderTimeouts.push(setTimeout(() => {
        document.getElementById('modal-loader')?.classList.remove('open');
    }, delay));
}

function _loaderError(msg) {
    _cancelarTimers();

    const el = document.getElementById('loader-texto');
    if (el) {
        el.textContent = '✗ ' + msg;
        el.style.color = 'var(--red)';
    }

    _setBarra(100);
    document.getElementById('loader-barra')
        ?.style.setProperty('background', 'var(--red)');
}

/* ───────────────── PDF ───────────────── */

async function procesarPdf() {
    const inputEl = document.getElementById('input-pdf');
    const file    = inputEl?.files[0];

    if (!file || !file.type.includes('pdf')) return;

    resetImportacion();
    _procesando = true;
    iniciarLoader();

    let ok = false;

    try {
        const fd = new FormData();
        fd.append('data', file);

        const res = await fetch(WEBHOOK_PDF, { method: 'POST', body: fd });

        // FIX: si la respuesta no es OK a nivel HTTP, lanzamos error antes de parsear
        if (!res.ok) {
            throw new Error(`Error del servidor (${res.status})`);
        }

        let data;
        try {
            data = await res.json();
        } catch {
            throw new Error('Respuesta inválida del servidor');
        }

        if (!data?.ok || !Array.isArray(data.ofertas) || !data.ofertas.length) {
            throw new Error(data?.error || 'Sin ofertas extraídas');
        }

        ofertasImportadas = data.ofertas;

        document.getElementById('btn-ver-tabla')
            ?.style.setProperty('display', 'inline-flex');

        ok = true;

        // FIX: esperamos a que el loader cierre (400ms) antes de abrir revisión
        _loaderTimeouts.push(setTimeout(() => mostrarRevision(ofertasImportadas), 500));

    } catch (err) {
        console.error('[procesarPdf]', err);
        _loaderError(err.message || 'Error de conexión');
    } finally {
    const delay = ok ? 400 : 2800;

    _loaderTimeouts.push(setTimeout(() => {
        document.getElementById('modal-loader')?.classList.remove('open');

        // 🔥 SOLO si hay datos, pasas a la siguiente pantalla
        if (ok) {
            mostrarRevision(ofertasImportadas);
        }
    }, delay));

    _procesando = false;

    if (inputEl) inputEl.value = '';
}
}
