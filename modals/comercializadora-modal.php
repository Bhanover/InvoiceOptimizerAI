<?php
/**
 * InvoiceOptimizer Ai — Modales comercializadoras
 */
?>

<!-- Modal Crear -->
<div id="modal-crear-com" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Nueva comercializadora</div>
            <button class="modal-close" onclick="cerrarModalesCom()">✕</button>
        </div>
        <form method="POST" action="?action=comercializadoras">
            <input type="hidden" name="crear_com" value="1">

            <label class="form-label">Nombre *</label>
            <input type="text" name="nombre" class="form-input" required placeholder="Plenitude, Iberdrola...">

            <label class="form-label" style="margin-top:10px">Email de contacto</label>
            <input type="email" name="email_contacto" class="form-input" placeholder="contratos@comercializadora.com">

            <label class="form-label" style="margin-top:10px">Teléfono</label>
            <input type="text" name="telefono" class="form-input" placeholder="900 000 000">

            <label class="form-label" style="margin-top:10px">Dirección</label>
            <input type="text" name="direccion" class="form-input" placeholder="Calle Ejemplo 1, Madrid">

            <label class="form-label" style="margin-top:10px;display:flex;align-items:center;gap:8px">
                <input type="checkbox" name="activa" checked> Activa
            </label>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" style="flex:1">Crear comercializadora</button>
                <button type="button" class="btn btn-ghost" onclick="cerrarModalesCom()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar -->
<div id="modal-editar-com" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Editar comercializadora</div>
            <button class="modal-close" onclick="cerrarModalesCom()">✕</button>
        </div>
        <form method="POST" action="?action=comercializadoras" id="form-editar-com">
            <input type="hidden" name="editar_com" value="1">
            <input type="hidden" name="com_id" value="">

            <label class="form-label">Nombre *</label>
            <input type="text" name="nombre" class="form-input" required>

            <label class="form-label" style="margin-top:10px">Email de contacto</label>
            <input type="email" name="email_contacto" class="form-input">

            <label class="form-label" style="margin-top:10px">Teléfono</label>
            <input type="text" name="telefono" class="form-input">

            <label class="form-label" style="margin-top:10px">Dirección</label>
            <input type="text" name="direccion" class="form-input">

            <label class="form-label" style="margin-top:10px;display:flex;align-items:center;gap:8px">
                <input type="checkbox" name="activa"> Activa
            </label>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" style="flex:1">Guardar cambios</button>
                <button type="button" class="btn btn-ghost" onclick="cerrarModalesCom()">Cancelar</button>
            </div>
        </form>
    </div>
</div>
