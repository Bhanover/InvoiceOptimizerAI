<?php
require_once __DIR__ . '/../queries/errores.queries.php';

$log_errores = q_errores_recientes($pdo, 50);
?>

<div class="section-title">Log de errores</div>
<div class="section-sub">Últimos 50 errores del sistema de automatización</div>

<div class="card">
    <?php foreach ($log_errores as $e): ?>
    <div class="log-item">
        <span class="log-motivo <?= str_contains(strtolower($e['motivo'] ?? ''), 'extrac') ? 'extraccion' : '' ?>">
            <?= htmlspecialchars($e['motivo'] ?? '—') ?>
        </span>
        <div class="log-info">
            <div class="log-email"><?= htmlspecialchars($e['email_cliente'] ?: 'Sin email') ?></div>
            <div class="log-meta">
                <?= htmlspecialchars($e['nombre_cliente'] ?: $e['nombre_cliente_rel'] ?: '—') ?>
                <?php if ($e['factura_id']): ?>
                · Factura: <span class="id-display" style="font-size:0.66rem">#<?= $e['factura_id'] ?></span>
                <?php endif; ?>
                <?php if ($e['cliente_id_rel']): ?>
                · <a href="?action=cliente&id=<?= $e['cliente_id_rel'] ?>" style="color:var(--green);font-size:0.72rem;text-decoration:none">Ver cliente</a>
                <?php endif; ?>
                · <?= $e['created_at'] ? date('d/m/Y H:i', strtotime($e['created_at'])) : '—' ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (!$log_errores): ?>
    <p style="text-align:center;padding:48px;color:var(--text3)">
        <span style="font-size:1.8rem;display:block;margin-bottom:8px">✓</span>
        No hay errores registrados
    </p>
    <?php endif; ?>
</div>
