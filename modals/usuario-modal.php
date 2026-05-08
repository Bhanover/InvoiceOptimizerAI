<?php /** InvoiceOptimizer Ai — Modal editar usuario — solo admin */ ?>

<div id="modal-editar-usuario" class="modal-overlay">
    <div class="modal-box" style="max-width:440px">
        <div class="modal-header">
            <div class="modal-title">Editar usuario</div>
            <button class="modal-close" onclick="cerrarModalUsuario()">✕</button>
        </div>
        <form method="POST" action="?action=usuarios" id="form-editar-usuario">
            <input type="hidden" name="editar_usuario" value="1">
            <input type="hidden" name="usuario_id" id="edit-usuario-id">

            <label class="form-label">Nombre</label>
            <div id="edit-usuario-nombre" class="modal-dato"></div>

            <label class="form-label">Email</label>
            <div id="edit-usuario-email" class="modal-dato" style="color:var(--text2)"></div>

            <label class="form-label">Rol</label>
            <select name="rol" id="edit-usuario-rol" class="form-input" style="width:100%;margin-bottom:14px">
                <option value="viewer">Viewer</option>
                <option value="admin">Admin</option>
            </select>

            <label class="form-label">
                Nueva contraseña
                <span style="color:var(--text3);font-weight:400;text-transform:none;letter-spacing:0"> — dejar vacío para no cambiar</span>
            </label>
            <input type="password" name="new_password" class="form-input" placeholder="Mínimo 6 caracteres" style="width:100%;margin-bottom:14px">

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" style="flex:1">Guardar cambios</button>
                <button type="button" class="btn btn-ghost" onclick="cerrarModalUsuario()">Cancelar</button>
            </div>
        </form>
    </div>
</div>
