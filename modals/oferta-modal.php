<?php
/**
 * InvoiceOptimizer Ai — Modales de oferta (crear + editar + revisión PDF)
 * Incluido desde pages/ofertas.php — solo si es_admin()
 */
?>
 
<!-- Modal Crear -->
<div id="modal-crear" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Nueva oferta</div>
            <button class="modal-close" onclick="cerrarModales()">✕</button>
        </div>
        <form method="POST" action="?action=ofertas">
            <input type="hidden" name="crear_oferta" value="1">
            <?php include __DIR__ . '/oferta-fields.php'; ?>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" style="flex:1">Crear oferta</button>
                <button type="button" class="btn btn-ghost" onclick="cerrarModales()">Cancelar</button>
            </div>
        </form>
    </div>
</div>
 
<!-- Modal Editar -->
<div id="modal-editar" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Editar oferta</div>
            <button class="modal-close" onclick="cerrarModales()">✕</button>
        </div>
        <form method="POST" action="?action=ofertas" id="form-editar">
            <input type="hidden" name="editar_oferta" value="1">
            <input type="hidden" name="oferta_id"    value="">
            <?php include __DIR__ . '/oferta-fields.php'; ?>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" style="flex:1">Guardar cambios</button>
                <button type="button" class="btn btn-ghost" onclick="cerrarModales()">Cancelar</button>
            </div>
        </form>
    </div>
</div>
 
<!-- Modal Revisión PDF -->
<div id="modal-revision" class="modal-overlay">
    <div class="modal-box modal-box--wide">
        <div class="modal-header">
            <div class="modal-title">Revisar ofertas extraídas</div>
            <button class="modal-close" onclick="cancelarImportacion()">✕</button>
        </div>
 
        <div class="modal-body">
            <div class="tabla-scroll">
                <div id="tabla-revision"></div>
            </div>
        </div>
 
        <form method="POST" action="?action=ofertas" id="form-importar">
            <input type="hidden" name="importar_ofertas"      value="1">
            <input type="hidden" name="ofertas_json"          id="ofertas-json-input">
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" style="flex:1">✓ Guardar todas</button>
                <button type="button" class="btn btn-ghost"
                        onclick="cancelarImportacion()">Cancelar</button>
            </div>
        </form>
 
    </div>
</div>

 
<!-- Modal Loader PDF -->
<div id="modal-loader" class="modal-overlay modal-loader-overlay">
    <div class="modal-box modal-loader-box">
 
        <div class="loader-icon">
            <svg viewBox="0 0 50 50" class="loader-spinner">
                <circle cx="25" cy="25" r="20"
                        fill="none" stroke="var(--border2)" stroke-width="3"/>
                <circle cx="25" cy="25" r="20"
                        fill="none" stroke="var(--cyan)" stroke-width="3"
                        stroke-dasharray="30 100" stroke-linecap="round"
                        class="loader-arc"/>
            </svg>
        </div>
 
        <div id="loader-texto" class="loader-texto">Iniciando...</div>
 
        <div class="loader-barra-wrap">
            <div class="loader-barra-track">
                <div id="loader-barra" class="loader-barra-fill"></div>
            </div>
            <div class="loader-barra-pct" id="loader-pct"></div>
        </div>
 
        <div class="loader-dots">
            <?php for ($i = 0; $i < 5; $i++): ?>
            <div class="loader-dot <?= $i === 0 ? 'active' : '' ?>"></div>
            <?php endfor; ?>
        </div>
 
        <p class="loader-hint">
            La IA está analizando las tarifas. Esto puede tardar hasta 1 minuto.
        </p>
 
    </div>
</div>
