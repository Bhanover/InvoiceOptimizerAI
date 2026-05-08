/**
 * InvoiceOptimizer Ai — Modal editar usuario
 */

function abrirModalEditarUsuario(usuario) {
    document.getElementById('edit-usuario-id').value           = usuario.id;
    document.getElementById('edit-usuario-nombre').textContent = usuario.nombre;
    document.getElementById('edit-usuario-email').textContent  = usuario.email;
    document.getElementById('edit-usuario-rol').value          = usuario.rol;
    document.querySelector('#form-editar-usuario [name="new_password"]').value = '';
    document.getElementById('modal-editar-usuario').classList.add('open');
}

function cerrarModalUsuario() {
    document.getElementById('modal-editar-usuario').classList.remove('open');
}

document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('modal-editar-usuario');
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) cerrarModalUsuario();
        });
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') cerrarModalUsuario();
});
