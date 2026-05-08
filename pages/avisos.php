<?php
require_once __DIR__ . '/../queries/seguimientos.queries.php';

if (!$pdo) {
    echo "<div class='alert alert-error'>Error de conexión.</div>";
    exit;
}

/* ─────────────────────────────
   POST ACTIONS
───────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && es_admin()) {

    if (isset($_POST['cerrar_seguimiento'])) {

        q_seguimiento_cerrar($pdo, (int)$_POST['seg_id']);

        header("Location: ?action=avisos&msg=" . urlencode('Aviso gestionado.'));
        exit;
    }

    if (isset($_POST['guardar_config'])) {

        $dias_recontacto_inicial =
            ((int)$_POST['anos_recontacto_inicial'] * 365) +
            ((int)$_POST['meses_recontacto_inicial'] * 30) +
            (int)$_POST['dias_recontacto_inicial'];

        $dias_segundo_contacto =
            ((int)$_POST['anos_segundo_contacto'] * 365) +
            ((int)$_POST['meses_segundo_contacto'] * 30) +
            (int)$_POST['dias_segundo_contacto'];

        q_config_seguimientos_update(
            $pdo,
            $dias_recontacto_inicial,
            $dias_segundo_contacto,
            (int)$_POST['bloqueo_dias']
        );

        header("Location: ?action=avisos&msg=" . urlencode('Configuración guardada.'));
        exit;
    }

    if (isset($_POST['recalcular_seguimientos'])) {

        $config = q_config_seguimientos($pdo);

        $pdo->prepare("
            UPDATE seguimientos
            SET fecha_proximo_contacto =
                DATE_ADD(fecha_inicio, INTERVAL ? DAY)
            WHERE estado = 'activo'
        ")->execute([$config['dias_recontacto_inicial']]);

        header("Location: ?action=avisos&msg=" . urlencode('Seguimientos recalculados.'));
        exit;
    }
}

/* ─────────────────────────────
   DATOS
───────────────────────────── */
$avisos_raw = q_avisos_activos($pdo);
$avisos     = $avisos_raw;
$total      = count($avisos);
$is_admin   = es_admin();
?>

<link rel="stylesheet" href="/css/avisos.css">
<link rel="stylesheet" href="/css/config-seguimientos.css">

<!-- HEADER -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;flex-wrap:wrap;gap:10px">

    <div class="section-title">Seguimientos de renovación</div>

    <?php if ($is_admin): ?>
    <div class="avisos-actions">

        <button class="btn btn-ghost btn-sm"
                onclick="openModal('modal-config-seguimientos')">
            ⚙ Configuración
        </button>

        <form method="POST">
            <button class="btn btn-ghost btn-sm" name="recalcular_seguimientos">
                🔄 Recalcular
            </button>
        </form>

    </div>
    <?php endif; ?>

</div>

<div class="section-sub">
    <?= $total ?> clientes pendientes
    <?= $total > 0 ? '<span style="color:var(--green)">· ordenados por prioridad</span>' : '' ?>
</div>

<!-- TABLE -->
<div class="card" style="padding:0;margin-top:30px">
<div class="table-wrap">

    <table>

        <thead>
            <tr>
                <th>Cliente</th>
                <th>CUPS</th>
                <th>Oferta y ahorro actual</th>
                <th>Días</th>
                <th>Estado</th>
                <th style="text-align:right">Gestionado</th>
            </tr>
        </thead>

        <tbody>

        <?php if (empty($avisos)): ?>
        <tr>
            <td colspan="6" class="empty-state">
                Sin seguimientos pendientes
            </td>
        </tr>
        <?php endif; ?>

        <?php foreach ($avisos as $a): ?>

        <?php
            $dias_retraso = (int)$a['retraso'];

            $diasLabel = $dias_retraso === 0
                ? 'hoy'
                : 'hace ' . $dias_retraso . 'd';

            $estado = $a['estado_label'];

            /* mapa prioridad → color días */
            $colorMap = [
                1 => '#d97706',
                2 => '#f97316',
                3 => '#dc2626',
                4 => '#7f1d1d',
            ];
            $color = $colorMap[$a['prioridad']] ?? '#6b7280';

            /* badge estado */
            $badgeMap = [
                'Urgente'   => 'urgente',
                'Aviso'     => 'aviso',
                'Pendiente' => 'pendiente',
                'Ok'        => 'ok',
            ];
            $badgeClass = $badgeMap[$estado] ?? 'pendiente';

            /* oferta actual */
            $nombre_oferta    = htmlspecialchars($a['nombre_oferta'] ?? '');
            $ahorro           = $a['ahorro_mensual'] !== null
                        ? number_format((float)$a['ahorro_mensual'], 2) . ' €/mes'
                        : null;
            
        ?>

        <tr onclick="window.location='?action=cliente&id=<?= $a['cliente_id'] ?>'">

            <td>
                <strong><?= htmlspecialchars($a['nombre_cliente']) ?></strong>
            </td>

            <td class="mono text-sm">
                <?= htmlspecialchars(substr($a['cups'] ?? '', -8)) ?>
            </td>

            <td>
                <?php if ($nombre_oferta): ?>
                    <div class="text-sm" style="color:var(--text3)"><?= $nombre_oferta ?></div>
                <?php endif; ?>
                <?php if ($ahorro): ?>
                    <div class="text-sm" style="color:var(--green);font-weight:600">+<?= $ahorro ?></div>
                <?php endif; ?>
            </td>

            <td style="color:<?= $color ?>; font-weight:600">
                <?= $diasLabel ?>
            </td>

            <td>
                <span class="badge badge-<?= $badgeClass ?>">
                    <?= htmlspecialchars($estado) ?>
                </span>
            </td>

            <td onclick="event.stopPropagation()" style="text-align:right">

                <form method="POST">
                    <input type="hidden" name="seg_id" value="<?= $a['seg_id'] ?>">
                    <button class="btn-icon btn-danger"
                            name="cerrar_seguimiento"
                            title="Marcar como gestionado">
                        ✓
                    </button>
                </form>

            </td>

        </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>
</div>

<?php if ($is_admin): ?>
<?php require __DIR__ . '/../modals/config-seguimientos.php'; ?>
<?php endif; ?>

<script src="/js/avisos.js"></script>
<script>lucide.createIcons();</script>