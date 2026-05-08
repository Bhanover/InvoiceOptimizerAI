<?php
// pages/factura.php
require_once __DIR__ . '/../queries/factura.queries.php';

$id  = (int)($_GET['id']  ?? 0);
$cid = (int)($_GET['cid'] ?? 0);
if (!$id) { echo '<div class="alert alert-error">ID de factura no válido.</div>'; return; }

$factura = q_factura_by_id($pdo, $id);

if (!$factura || (int)$factura['cliente_id'] !== $cid) {
    echo '<div class="alert alert-error">Factura no encontrada.</div>';
    return;
}
$cliente = q_factura_cliente($pdo, (int)$factura['cliente_id']);
$oferta  = $factura['oferta_asignada']
    ? q_factura_oferta($pdo, (int)$factura['oferta_asignada'])
    : null;

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
    'oferta_enviada'             => 'Oferta enviada',
    'derivado_comercializadora'  => 'Derivado',
    'contratado'                 => 'Contratado',
    'rechazado_cliente'          => 'Rechazado cliente',
    'rechazado_comercializadora' => 'Rechazado com.',
];

$estado_badge = $badge_map[$factura['estado']] ?? 'pendiente';
$estado_label = $blabel_map[$factura['estado']] ?? $factura['estado'];

$n   = fn($v, int $d=2) => ($v !== null && $v !== '') ? number_format((float)$v, $d, ',', '.') : '—';
$fd  = fn($v) => $v ? date('d/m/Y', strtotime($v)) : null;
$fdt = fn($v) => $v ? date('d/m/Y H:i', strtotime($v)) : null;
$pct = fn($v) => ($v !== null && $v !== '') ? number_format((float)$v, 2, ',', '.').' %' : '—';

// Detectar cuántos periodos tiene esta factura (P1-P6)
$periodos = [];
foreach (range(1, 6) as $p) {
    if ($factura["consumo_p$p"] !== null || $factura["precio_energia_p$p"] !== null || $factura["potencia_p$p"] !== null) {
        $periodos[] = $p;
    }
}
if (empty($periodos)) $periodos = [1, 2];
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="?action=clientes">Clientes</a> ›
    <a href="?action=cliente&id=<?= $factura['cliente_id'] ?>">
        <?= htmlspecialchars($cliente['nombre_cliente'] ?? '#'.$factura['cliente_id']) ?>
    </a> ›
    <span class="active">Factura #<?= $id ?></span>
</div>

<!-- Cabecera -->
<div class="factura-header">
    <div class="factura-header-left">
        <h1>Factura <span class="factura-id">#<?= $id ?></span></h1>
        <div class="factura-meta">
            <span class="badge badge-<?= $estado_badge ?>"><?= $estado_label ?></span>
            <?php if ($factura['tipo_contrato']): ?>
            <span class="factura-tag"><?= htmlspecialchars($factura['tipo_contrato']) ?></span>
            <?php endif; ?>
            <?php if ($factura['periodo_inicio'] && $factura['periodo_fin']): ?>
            <span><?= $fd($factura['periodo_inicio']) ?> → <?= $fd($factura['periodo_fin']) ?></span>
            <?php endif; ?>
            <?php if ($factura['dias_facturacion']): ?>
            <span><?= (int)$factura['dias_facturacion'] ?> días facturados</span>
            <?php endif; ?>
            <?php if ($fdt($factura['timestamp_entrada'])): ?>
            <span>Entrada: <?= $fdt($factura['timestamp_entrada']) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div style="display:flex;gap:6px;align-items:center">
        <?php if ($factura['pdf_nombre']): ?>
        <a href="/pdfs/<?= htmlspecialchars($factura['pdf_nombre']) ?>" target="_blank" class="btn btn-ghost btn-sm">↓ Ver PDF</a>
        <?php endif; ?>
        <a href="?action=cliente&id=<?= $factura['cliente_id'] ?>&tab=facturas" class="btn btn-ghost btn-sm">← Volver</a>
    </div>
</div>

<!-- Cuerpo principal: dos columnas -->
<div class="factura-body">

    <!-- Columna izquierda -->
    <div class="factura-main">
        <div class="card factura-datos-card">

            <!-- Suministro -->
            <div class="fblock">
                <div class="fblock-title">Suministro</div>
                <div class="frow"><span class="fl">Titular</span><span class="fv"><?= htmlspecialchars($factura['titular'] ?: '—') ?></span></div>
                <div class="frow"><span class="fl">Dirección</span><span class="fv"><?= htmlspecialchars($factura['direccion_suministro'] ?: '—') ?></span></div>
                <div class="frow"><span class="fl">CUPS</span><span class="fv mono"><?= htmlspecialchars($factura['cups'] ?: '—') ?></span></div>
                <div class="frow"><span class="fl">Tipo contrato</span><span class="fv"><?= htmlspecialchars($factura['tipo_contrato'] ?: '—') ?></span></div>
                <div class="frow"><span class="fl">Comercializadora actual</span><span class="fv"><?= htmlspecialchars($factura['comercializadora_actual'] ?: '—') ?></span></div>
                <?php if ($factura['tiene_precio_unico']): ?>
                <div class="frow">
                    <span class="fl">Precio único</span>
                    <span class="fv"><span class="badge badge-revision">Sí</span></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="fdivider"></div>

            <!-- Potencia contratada P1-P6 -->
            <div class="fblock">
                <div class="fblock-title">Potencia contratada</div>
                <div class="factura-tabla-compacta">
                    <?php $cols = count($periodos); ?>
                    <div class="ftc-head" style="grid-template-columns: 1fr <?= str_repeat('80px ', $cols) ?>">
                        <span></span>
                        <?php foreach ($periodos as $p): ?><span>P<?= $p ?></span><?php endforeach; ?>
                    </div>
                    <div class="ftc-row" style="grid-template-columns: 1fr <?= str_repeat('80px ', $cols) ?>">
                        <span class="fl">kW</span>
                        <?php foreach ($periodos as $p): ?>
                        <span class="fv mono"><?= $factura["potencia_p$p"] !== null ? $n($factura["potencia_p$p"], 3) : '—' ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="ftc-row" style="grid-template-columns: 1fr <?= str_repeat('80px ', $cols) ?>">
                        <span class="fl">€/kW·día</span>
                        <?php foreach ($periodos as $p): ?>
                        <span class="fv mono"><?= $factura["precio_potencia_p$p"] !== null ? $n($factura["precio_potencia_p$p"], 5) : '—' ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="fdivider"></div>

            <!-- Consumo y precio energía P1-P6 -->
            <div class="fblock">
                <div class="fblock-title">Consumo y precio energía</div>
                <div class="factura-tabla-compacta">
                    <div class="ftc-head" style="grid-template-columns: 1fr <?= str_repeat('80px ', $cols) ?>">
                        <span></span>
                        <?php foreach ($periodos as $p): ?><span>P<?= $p ?></span><?php endforeach; ?>
                    </div>
                    <div class="ftc-row" style="grid-template-columns: 1fr <?= str_repeat('80px ', $cols) ?>">
                        <span class="fl">kWh</span>
                        <?php foreach ($periodos as $p): ?>
                        <span class="fv mono"><?= $factura["consumo_p$p"] !== null ? $n($factura["consumo_p$p"], 0) : '—' ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="ftc-row" style="grid-template-columns: 1fr <?= str_repeat('80px ', $cols) ?>">
                        <span class="fl">€/kWh</span>
                        <?php foreach ($periodos as $p): ?>
                        <span class="fv mono"><?= $factura["precio_energia_p$p"] !== null ? $n($factura["precio_energia_p$p"], 5) : '—' ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="fdivider"></div>

            <!-- Importes -->
            <div class="fblock">
                <div class="fblock-title">Importes</div>
                <div class="frow"><span class="fl">Energía</span><span class="fv mono"><?= $factura['importe_energia'] !== null ? $n($factura['importe_energia']).' €' : '—' ?></span></div>
                <div class="frow"><span class="fl">Potencia</span><span class="fv mono"><?= $factura['importe_potencia'] !== null ? $n($factura['importe_potencia']).' €' : '—' ?></span></div>
                <?php if ($factura['alquiler_contador'] !== null): ?>
                <div class="frow"><span class="fl">Alquiler contador</span><span class="fv mono"><?= $n($factura['alquiler_contador']) ?> €</span></div>
                <?php endif; ?>
                <?php if ($factura['gasto_gestion'] !== null): ?>
                <div class="frow"><span class="fl">Gasto gestión</span><span class="fv mono"><?= $n($factura['gasto_gestion']) ?> €</span></div>
                <?php endif; ?>
                <?php if ($factura['descuento_energia_pct'] !== null || $factura['descuento_energia_importe'] !== null): ?>
                <div class="frow">
                    <span class="fl">Descuento energía</span>
                    <span class="fv mono" style="color:var(--green)">
                        <?php
                        $parts = [];
                        if ($factura['descuento_energia_pct'] !== null)     $parts[] = $pct($factura['descuento_energia_pct']);
                        if ($factura['descuento_energia_importe'] !== null) $parts[] = $n($factura['descuento_energia_importe']).' €';
                        echo implode(' / ', $parts);
                        ?>
                    </span>
                </div>
                <?php endif; ?>
                <?php if ($factura['impuesto_electrico'] !== null): ?>
                <div class="frow"><span class="fl">Impuesto eléctrico</span><span class="fv mono"><?= $pct($factura['impuesto_electrico']) ?></span></div>
                <?php endif; ?>
                <?php if ($factura['iva'] !== null): ?>
                <div class="frow"><span class="fl">IVA</span><span class="fv mono"><?= $factura['iva'] !== null ? $n($factura['iva'],2).' €' : '—' ?></span></div>
                <?php endif; ?>
                <div class="frow frow-total">
                    <span class="fl">Total factura</span>
                    <span class="fv mono fv-total"><?= $factura['importe_total'] !== null ? $n($factura['importe_total']).' €' : '—' ?></span>
                </div>
            </div>

        </div>
    </div>

    <!-- Columna derecha -->
    <div class="factura-side">

        <!-- Resultado de gestión -->
        <div class="card">
            <div class="card-header"><div class="card-title">Resultado de gestión</div></div>
            <div class="frow"><span class="fl">Comercializadora ofertada</span><span class="fv"><?= htmlspecialchars($factura['comercializadora_ofertada'] ?: '—') ?></span></div>
            <div class="frow"><span class="fl">Ahorro mensual</span><span class="fv mono fv-ahorro"><?= $factura['ahorro_mensual'] !== null ? $n($factura['ahorro_mensual']).' €' : '—' ?></span></div>
            <div class="frow"><span class="fl">Comisión mensual</span><span class="fv mono"><?= $factura['comision_mensual'] !== null ? $n($factura['comision_mensual']).' €' : '—' ?></span></div>
            <div class="frow"><span class="fl">Fee aplicado</span><span class="fv mono"><?= $factura['fee_aplicado'] !== null ? $n($factura['fee_aplicado'], 5) : '—' ?></span></div>
        </div>

        <!-- Fechas del proceso -->
        <?php
        $fechas = array_filter([
            'Entrada'        => $fdt($factura['timestamp_entrada']),
            'Oferta enviada' => $fdt($factura['fecha_oferta_enviada']),
            'Derivación'     => $fdt($factura['fecha_derivacion']),
            'Contratación'   => $fdt($factura['fecha_contratacion']),
            'Rechazo'        => $fdt($factura['fecha_rechazo']),
        ]);
        if ($fechas): ?>
        <div class="card">
            <div class="card-header"><div class="card-title">Fechas del proceso</div></div>
            <?php foreach ($fechas as $lbl => $val): ?>
            <div class="fecha-item">
                <div class="fecha-dot"></div>
                <div><div class="fl"><?= $lbl ?></div><div class="fv mono"><?= $val ?></div></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- Oferta asignada -->
<div class="card oferta-card">
    <div class="card-header">
        <div class="card-title">Oferta asignada</div>
        <?php if ($oferta): ?>
        <span class="badge <?= $oferta['activa'] ? 'badge-contratado' : 'badge-descartado' ?>">
            <?= $oferta['activa'] ? 'Activa' : 'Inactiva' ?>
        </span>
        <?php endif; ?>
    </div>

    <?php if ($oferta): ?>
    <div class="oferta-nombre-row">
        <span class="oferta-nombre"><?= htmlspecialchars($oferta['nombre_oferta']) ?></span>
        <span class="oferta-com"><?= htmlspecialchars($oferta['comercializadora']) ?></span>
        <?php if ($oferta['email_comercializadora']): ?>
        <span class="oferta-email"><?= htmlspecialchars($oferta['email_comercializadora']) ?></span>
        <?php endif; ?>
        <?php if ($oferta['tipo_tarifa']): ?>
        <span class="factura-tag"><?= htmlspecialchars($oferta['tipo_tarifa']) ?></span>
        <?php endif; ?>
        <?php if ($oferta['version_tarifa']): ?>
        <span class="factura-tag"><?= htmlspecialchars($oferta['version_tarifa']) ?></span>
        <?php endif; ?>
    </div>

    <?php
    // Detectar periodos de la oferta
    $periodos_oferta = [];
    foreach (range(1, 6) as $p) {
        if ($oferta["precio_energia_p$p"] !== null || $oferta["precio_potencia_p$p"] !== null) {
            $periodos_oferta[] = $p;
        }
    }
    if (empty($periodos_oferta)) $periodos_oferta = [1, 2];
    $cols_oferta = count($periodos_oferta);
    ?>

    <div class="oferta-precios" style="grid-template-columns: repeat(<?= $cols_oferta ?>, 1fr)">
        <?php foreach ($periodos_oferta as $p): ?>
        <div class="oferta-precio-item">
            <div class="oferta-precio-label">Energía P<?= $p ?></div>
            <div class="oferta-precio-val"><?= $oferta["precio_energia_p$p"] !== null ? $n($oferta["precio_energia_p$p"], 5) : '—' ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="oferta-precios" style="grid-template-columns: repeat(<?= $cols_oferta ?>, 1fr); margin-top:6px">
        <?php foreach ($periodos_oferta as $p): ?>
        <div class="oferta-precio-item">
            <div class="oferta-precio-label">Potencia P<?= $p ?></div>
            <div class="oferta-precio-val"><?= $oferta["precio_potencia_p$p"] !== null ? $n($oferta["precio_potencia_p$p"], 5) : '—' ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="display:flex;gap:16px;margin-top:12px;flex-wrap:wrap">
        <?php if ($oferta['fee_minimo'] !== null): ?>
        <div class="frow" style="flex:1;min-width:120px"><span class="fl">Fee mínimo</span><span class="fv mono"><?= $n($oferta['fee_minimo'], 5) ?></span></div>
        <?php endif; ?>
        <?php if ($oferta['fee_maximo'] !== null): ?>
        <div class="frow" style="flex:1;min-width:120px"><span class="fl">Fee máximo</span><span class="fv mono"><?= $n($oferta['fee_maximo'], 5) ?></span></div>
        <?php endif; ?>
    </div>

    <?php elseif ($factura['oferta_asignada']): ?>
    <p class="oferta-missing">ID #<?= (int)$factura['oferta_asignada'] ?> — esta oferta ya no existe en el catálogo.</p>
    <?php else: ?>
    <p class="oferta-vacia">No se ha asignado ninguna oferta a esta factura.</p>
    <?php endif; ?>
</div>

<?php if ($factura['factura-tag'] ?? false): // placeholder por si añades más bloques ?>
<?php endif; ?>