<?php
if (!isset($pdo) || !es_admin()) return;

$config = q_config_seguimientos($pdo);

function split_time($dias) {
    $anos  = floor($dias / 365);
    $resto = $dias % 365;
    $meses = floor($resto / 30);
    $dias_rest = $resto % 30;
    return [$anos, $meses, $dias_rest];
}

[$a1, $m1, $d1] = split_time($config['dias_recontacto_inicial']);
[$a2, $m2, $d2] = split_time($config['dias_segundo_contacto']);
?>

<div class="modal-overlay" id="modal-config-seguimientos">
    <div class="modal-box cseg-box">

        <!-- HEADER -->
        <div class="cseg-header">
            <div class="cseg-header-left">
                <div class="cseg-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                </div>
                <div>
                    <div class="cseg-title">Configuración de seguimientos</div>
                    <div class="cseg-subtitle">Tiempos de contacto y bloqueo</div>
                </div>
            </div>
            <button class="modal-close" onclick="closeModal('modal-config-seguimientos')">×</button>
        </div>

        <form method="POST" action="?action=avisos">

            <!-- BLOQUE 1: PRIMER AVISO -->
            <div class="cseg-section">
                <div class="cseg-section-label">
                    <span class="cseg-dot cseg-dot--green"></span>
                    Primer aviso desde contratación
                </div>
                <div class="cseg-section-hint">
                    Tiempo que debe pasar desde la contratación antes de generar el primer aviso
                </div>

                <div class="cseg-time-row">
                    <div class="cseg-time-field">
                        <label class="cseg-field-label">Años</label>
                        <div class="cseg-number-wrap">
                            <button type="button" class="cseg-step cseg-step--down" data-target="anos_recontacto_inicial">−</button>
                            <input type="number" name="anos_recontacto_inicial"
                                   id="anos_recontacto_inicial"
                                   min="0" value="<?= $a1 ?>"
                                   class="cseg-number-input">
                            <button type="button" class="cseg-step cseg-step--up" data-target="anos_recontacto_inicial">+</button>
                        </div>
                    </div>

                    <div class="cseg-time-sep">:</div>

                    <div class="cseg-time-field">
                        <label class="cseg-field-label">Meses</label>
                        <div class="cseg-number-wrap">
                            <button type="button" class="cseg-step cseg-step--down" data-target="meses_recontacto_inicial">−</button>
                            <input type="number" name="meses_recontacto_inicial"
                                   id="meses_recontacto_inicial"
                                   min="0" max="11" value="<?= $m1 ?>"
                                   class="cseg-number-input">
                            <button type="button" class="cseg-step cseg-step--up" data-target="meses_recontacto_inicial">+</button>
                        </div>
                    </div>

                    <div class="cseg-time-sep">:</div>

                    <div class="cseg-time-field">
                        <label class="cseg-field-label">Días</label>
                        <div class="cseg-number-wrap">
                            <button type="button" class="cseg-step cseg-step--down" data-target="dias_recontacto_inicial">−</button>
                            <input type="number" name="dias_recontacto_inicial"
                                   id="dias_recontacto_inicial"
                                   min="0" max="30" value="<?= $d1 ?>"
                                   class="cseg-number-input">
                            <button type="button" class="cseg-step cseg-step--up" data-target="dias_recontacto_inicial">+</button>
                        </div>
                    </div>

                    <div class="cseg-total-pill" id="preview-inicial">
                        <?= ($a1 * 365 + $m1 * 30 + $d1) ?> días
                    </div>
                </div>
            </div>

            <!-- DIVIDER -->
            <div class="cseg-divider"></div>

            <!-- BLOQUE 2: RECONTACTO -->
            <div class="cseg-section">
                <div class="cseg-section-label">
                    <span class="cseg-dot cseg-dot--amber"></span>
                    Recontacto al posponer
                </div>
                <div class="cseg-section-hint">
                    Tiempo añadido a la fecha de próximo contacto al posponer un seguimiento
                </div>

                <div class="cseg-time-row">
                    <div class="cseg-time-field">
                        <label class="cseg-field-label">Años</label>
                        <div class="cseg-number-wrap">
                            <button type="button" class="cseg-step cseg-step--down" data-target="anos_segundo_contacto">−</button>
                            <input type="number" name="anos_segundo_contacto"
                                   id="anos_segundo_contacto"
                                   min="0" value="<?= $a2 ?>"
                                   class="cseg-number-input">
                            <button type="button" class="cseg-step cseg-step--up" data-target="anos_segundo_contacto">+</button>
                        </div>
                    </div>

                    <div class="cseg-time-sep">:</div>

                    <div class="cseg-time-field">
                        <label class="cseg-field-label">Meses</label>
                        <div class="cseg-number-wrap">
                            <button type="button" class="cseg-step cseg-step--down" data-target="meses_segundo_contacto">−</button>
                            <input type="number" name="meses_segundo_contacto"
                                   id="meses_segundo_contacto"
                                   min="0" max="11" value="<?= $m2 ?>"
                                   class="cseg-number-input">
                            <button type="button" class="cseg-step cseg-step--up" data-target="meses_segundo_contacto">+</button>
                        </div>
                    </div>

                    <div class="cseg-time-sep">:</div>

                    <div class="cseg-time-field">
                        <label class="cseg-field-label">Días</label>
                        <div class="cseg-number-wrap">
                            <button type="button" class="cseg-step cseg-step--down" data-target="dias_segundo_contacto">−</button>
                            <input type="number" name="dias_segundo_contacto"
                                   id="dias_segundo_contacto"
                                   min="0" max="30" value="<?= $d2 ?>"
                                   class="cseg-number-input">
                            <button type="button" class="cseg-step cseg-step--up" data-target="dias_segundo_contacto">+</button>
                        </div>
                    </div>

                    <div class="cseg-total-pill" id="preview-segundo">
                        <?= ($a2 * 365 + $m2 * 30 + $d2) ?> días
                    </div>
                </div>
            </div>

            <!-- DIVIDER -->
            <div class="cseg-divider"></div>

            <!-- BLOQUE 3: BLOQUEO -->
            <div class="cseg-section cseg-section--inline">
                <div class="cseg-section-info">
                    <div class="cseg-section-label">
                        <span class="cseg-dot cseg-dot--red"></span>
                        Bloqueo tras gestión
                    </div>
                    <div class="cseg-section-hint">
                        Días que el aviso permanece oculto tras ser gestionado
                    </div>
                </div>
                <div class="cseg-number-wrap">
                    <button type="button" class="cseg-step cseg-step--down" data-target="bloqueo_dias">−</button>
                    <input type="number" name="bloqueo_dias"
                           id="bloqueo_dias"
                           min="0" value="<?= (int)$config['bloqueo_dias'] ?>"
                           class="cseg-number-input">
                    <button type="button" class="cseg-step cseg-step--up" data-target="bloqueo_dias">+</button>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="cseg-footer">
                <button type="button"
                        class="btn btn-ghost"
                        onclick="closeModal('modal-config-seguimientos')">
                    Cancelar
                </button>
                <button type="submit" name="guardar_config" class="btn btn-primary">
                    Guardar cambios
                </button>
            </div>

        </form>
    </div>
</div>

<script>
(function () {
    // ── Stepper buttons ────────────────────────────────────
    document.querySelectorAll('.cseg-step').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const step = btn.classList.contains('cseg-step--up') ? 1 : -1;
            const min  = input.min !== '' ? parseInt(input.min) : -Infinity;
            const max  = input.max !== '' ? parseInt(input.max) :  Infinity;
            input.value = Math.min(max, Math.max(min, parseInt(input.value || 0) + step));
            input.dispatchEvent(new Event('input'));
        });
    });

    // ── Live total preview ─────────────────────────────────
    function toDays(y, m, d) {
        return (parseInt(y) || 0) * 365 + (parseInt(m) || 0) * 30 + (parseInt(d) || 0);
    }

    function updatePreview(prefix, previewId) {
        const y = document.getElementById('anos_'  + prefix)?.value  ?? 0;
        const m = document.getElementById('meses_' + prefix)?.value  ?? 0;
        const d = document.getElementById('dias_'  + prefix)?.value  ?? 0;
        const total = toDays(y, m, d);
        const el = document.getElementById(previewId);
        if (el) el.textContent = total + ' días';
    }

    ['anos_recontacto_inicial','meses_recontacto_inicial','dias_recontacto_inicial'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', () => updatePreview('recontacto_inicial', 'preview-inicial'));
    });
    ['anos_segundo_contacto','meses_segundo_contacto','dias_segundo_contacto'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', () => updatePreview('segundo_contacto', 'preview-segundo'));
    });
})();
</script>