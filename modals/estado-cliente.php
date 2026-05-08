<?php if (es_admin()): ?>
<div id="modal-estado-cliente" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Cambiar estado</div>
            <button class="modal-close" onclick="cerrarModalEstado()">✕</button>
        </div>
        <form method="POST" action="?action=clientes">
            <input type="hidden" name="update_estado_factura" value="1">
            <input type="hidden" name="factura_id" id="modal-estado-factura-id">
            <input type="hidden" name="_redirect" value="cliente&id=<?= $id ?>">
            <label class="form-label">Nuevo estado</label>
            <select name="nuevo_estado" id="modal-estado-select" class="form-input">
                <?php foreach ($estados as $e): ?>
                    <option value="<?= $e ?>"><?= str_replace('_',' ',ucfirst($e)) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" style="flex:1">Guardar</button>
                <button type="button" class="btn btn-ghost" onclick="cerrarModalEstado()">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>