<?php
// pages/cliente.php
require_once __DIR__ . '/../queries/cliente.queries.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo '<div class="alert alert-error">ID de cliente no válido.</div>';
    return;
}

$cliente = q_cliente_by_id($pdo, $id);
if (!$cliente) {
    echo '<div class="alert alert-error">Cliente no encontrado.</div>';
    return;
}

$facturas = q_cliente_facturas($pdo, $id);

// Estados según el enum real de la DB
$badge_map = [
    'pendiente_oferta'           => 'pendiente',
    'sin_oferta'                 => 'sin-oferta',
    'oferta_enviada'             => 'enviada',
    'derivado_comercializadora'  => 'derivado',
    'contratado'                 => 'contratado',
    'rechazado_cliente'          => 'rechazado',
    'rechazado_comercializadora' => 'rechazado',
];
$blabel_map = [
    'pendiente_oferta'           => 'Pendiente',
    'sin_oferta'                 => 'Sin oferta',
    'oferta_enviada'             => 'Enviada',
    'derivado_comercializadora'  => 'Derivado',
    'contratado'                 => 'Contratado',
    'rechazado_cliente'          => 'Rechazado',
    'rechazado_comercializadora' => 'Rechazado com.',
];

$tab = $_GET['tab'] ?? 'resumen';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="?action=clientes">Clientes</a> ›
    <span class="active"><?= htmlspecialchars($cliente['nombre_cliente'] ?: '#'.$id) ?></span>
</div>

<!-- Cabecera -->
<div class="cliente-header">
    <div>
        <h1><?= htmlspecialchars($cliente['nombre_cliente'] ?: '—') ?></h1>
        <div class="cliente-meta">
            <span>#<?= $cliente['id'] ?></span>
            <span>Alta: <?= $cliente['fecha_alta'] ? date('d/m/Y', strtotime($cliente['fecha_alta'])) : '—' ?></span>

            <!-- Toggle estado cliente -->
            <?php if (es_admin()): ?>
            <form method="POST" style="display:inline">
                <input type="hidden" name="update_estado_cliente" value="1">
                <input type="hidden" name="cliente_id" value="<?= $cliente['id'] ?>">
                <input type="hidden" name="nuevo_estado_cliente"
                       value="<?= $cliente['estado'] === 'activo' ? 'inactivo' : 'activo' ?>">
                <button type="submit"
                        class="badge badge-<?= htmlspecialchars($cliente['estado']) ?>"
                        style="border:none;cursor:pointer;font-size:0.7rem;padding:2px 8px"
                        title="Clic para cambiar estado">
                    <?= ucfirst($cliente['estado'] ?? '—') ?> ↕
                </button>
            </form>
            <?php else: ?>
            <span class="badge badge-<?= htmlspecialchars($cliente['estado']) ?>">
                <?= ucfirst($cliente['estado'] ?? '—') ?>
            </span>
            <?php endif; ?>

        </div>
    </div>
    <div>
        <a href="?action=clientes" class="btn btn-ghost btn-sm">← Volver</a>
    </div>
</div>

<!-- Tabs -->
<div class="cliente-tabs">
    <button class="tab-btn <?= $tab==='resumen' ? 'active' : '' ?>" data-tab="resumen" onclick="switchTab('resumen')">Resumen</button>
    <button class="tab-btn <?= $tab==='facturas' ? 'active' : '' ?>" data-tab="facturas" onclick="switchTab('facturas')">
        Facturas <?php if(count($facturas)): ?><span class="tab-count"><?= count($facturas) ?></span><?php endif; ?>
    </button>
</div>

<!-- TAB: Resumen -->
<div id="tab-resumen" class="tab-panel <?= $tab==='resumen' ? 'active' : '' ?>">
    <div class="ficha-grid">
        <div class="card">
            <div class="card-header"><div class="card-title">Datos del cliente</div></div>
            <?php foreach ([
                'Nombre'   => $cliente['nombre_cliente'],
                'Email'    => $cliente['email_cliente'],
                'Teléfono' => $cliente['telefono_cliente'],
                'Estado'   => ucfirst($cliente['estado'] ?? '—'),
            ] as $lbl => $val): ?>
            <div class="ficha-row">
                <span class="ficha-label"><?= $lbl ?></span>
                <span class="ficha-val"><?= htmlspecialchars($val ?: '—') ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- TAB: Facturas -->
<div id="tab-facturas" class="tab-panel <?= $tab==='facturas' ? 'active' : '' ?>">
    <?php if ($facturas): ?>
    <div class="card table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Comercializadora</th>
                        <th>Periodo</th>
                        <th>Importe</th>
                        <th>Ahorro</th>
                        <th>Estado</th>
                        <th>Entrada</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facturas as $fh): ?>
                    <tr class="factura-row" onclick="window.location='?action=factura&id=<?= $fh['id'] ?>&cid=<?= $id ?>'" style="cursor:pointer">
                        <td>#<?= $fh['id'] ?></td>
                        <td><?= htmlspecialchars($fh['comercializadora_actual'] ?: '—') ?></td>
                        <td>
                            <?= $fh['periodo_inicio'] ? date('d/m/y', strtotime($fh['periodo_inicio'])) : '—' ?>
                            <?= $fh['periodo_fin'] ? ' → '.date('d/m/y', strtotime($fh['periodo_fin'])) : '' ?>
                        </td>
                        <td class="mono"><?= $fh['importe_total'] ? number_format($fh['importe_total'],2,',','.').'€' : '—' ?></td>
                        <td class="mono ahorro"><?= $fh['ahorro_mensual'] ? number_format($fh['ahorro_mensual'],2,',','.').'€' : '—' ?></td>
                        <td>
                            <span class="badge badge-<?= $badge_map[$fh['estado']] ?? 'pendiente' ?>">
                                <?= $blabel_map[$fh['estado']] ?? $fh['estado'] ?>
                            </span>
                        </td>
                        <td><?= $fh['timestamp_entrada'] ? date('d/m/y H:i', strtotime($fh['timestamp_entrada'])) : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
        <p class="no-data">Este cliente no tiene facturas registradas.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../modals/estado-cliente.php'; ?>
<script src="/js/cliente.js"></script>
