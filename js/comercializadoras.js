/**
 * InvoiceOptimizer Ai — Comercializadoras
 */

const abrirModalCrearCom = () =>
    document.getElementById('modal-crear-com')?.classList.add('open');

const cerrarModalesCom = () =>
    document.querySelectorAll('.modal-overlay.open')
        .forEach(m => m.classList.remove('open'));

function abrirModalEditarCom(com, e) {
    e?.stopPropagation();
    const f = document.getElementById('form-editar-com');
    if (!f) return;

    const set = (n, v) => {
        const el = f.querySelector(`[name="${n}"]`);
        if (el) el.value = v ?? '';
    };

    set('com_id',         com.id);
    set('nombre',         com.nombre);
    set('email_contacto', com.email_contacto);
    set('telefono',       com.telefono);
    set('direccion',      com.direccion);

    const chk = f.querySelector('[name="activa"]');
    if (chk) chk.checked = !!com.activa;

    document.getElementById('modal-editar-com')?.classList.add('open');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') cerrarModalesCom();
});

document.addEventListener('click', e => {
    const modal = document.querySelector('.modal-overlay.open');
    if (modal && e.target === modal) cerrarModalesCom();
});
