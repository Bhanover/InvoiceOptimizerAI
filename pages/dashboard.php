<?php
require_once __DIR__ . '/../queries/dashboard.queries.php';

$kpis          = q_dashboard_kpis($pdo);
$chart_dias    = q_dashboard_chart_dias($pdo);
$chart_estados = q_dashboard_chart_estados($pdo);
$chart_coms    = q_dashboard_chart_comercializadoras($pdo);
$ahorro_top    = q_dashboard_ahorro_top($pdo);
$acciones      = q_dashboard_acciones($pdo);

$conv = $kpis['total_facturas'] ? round(($kpis['contratados'] / $kpis['total_facturas']) * 100, 1) : 0;
?>

<div class="section-title">Resumen general</div>
<div class="section-sub">Pipeline comercial · Actualizado en tiempo real</div>

<!-- KPIs fila 1 -->
<div class="grid grid-4" style="margin-bottom:14px">
    <div class="kpi green">
        <div class="kpi-label">Clientes totales</div>
        <div class="kpi-value"><?= number_format($kpis['total_clientes'] ?? 0) ?></div>
        <div class="kpi-sub"><?= number_format($kpis['total_facturas'] ?? 0) ?> facturas analizadas</div>
    </div>
    <div class="kpi cyan">
        <div class="kpi-label">Contratados</div>
        <div class="kpi-value"><?= $kpis['contratados'] ?? 0 ?></div>
        <div class="kpi-sub"><?= $conv ?>% conversión</div>
    </div>
    <div class="kpi amber">
        <div class="kpi-label">Ahorro total generado</div>
        <div class="kpi-value"><?= number_format($kpis['ahorro_confirmado'] ?? 0, 0, ',', '.') ?>€</div>
        <div class="kpi-sub"><?= number_format($kpis['ahorro_pipeline'] ?? 0, 0, ',', '.') ?>€ en pipeline</div>
    </div>
    <div class="kpi blue">
        <div class="kpi-label">Comisión total</div>
        <div class="kpi-value"><?= number_format($kpis['comision_total'] ?? 0, 0, ',', '.') ?>€</div>
        <div class="kpi-sub">Ingresos generados</div>
    </div>
</div>

<!-- Acciones urgentes -->
<div class="card" style="margin-bottom:14px">
    <div class="card-header">
        <div class="card-title">Acciones urgentes</div>
    </div>
    <?php if ($acciones): ?>
        <div style="display:flex;flex-direction:column">
            <?php foreach ($acciones as $row): ?>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
                    <div>
                        <div style="font-size:0.85rem;font-weight:500;color:var(--text)">
                            <?= htmlspecialchars($row['nombre_cliente']) ?>
                        </div>
                        <div style="font-size:0.72rem;color:var(--text3)">
                            <?= str_replace('_', ' ', $row['estado']) ?>
                        </div>
                    </div>
                    <div style="font-size:0.75rem;color:var(--text3)">
                        <?= date('d M', strtotime($row['timestamp_entrada'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color:var(--text3);font-size:0.82rem;padding:20px;text-align:center">
            No hay acciones pendientes
        </p>
    <?php endif; ?>
</div>

<!-- Fila 1 gráficas -->
<div class="grid grid-2" style="margin-bottom:14px">
    <div class="card">
        <div class="card-header"><div class="card-title">Facturas por día — 30 días</div></div>
        <div class="chart-container"><canvas id="chartDias"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><div class="card-title">Distribución por estado</div></div>
        <div class="chart-container"><canvas id="chartEstados"></canvas></div>
    </div>
</div>

<!-- Fila 2 gráficas -->
<div class="grid grid-2" style="margin-bottom:14px">
    <div class="card">
        <div class="card-header"><div class="card-title">Resumen por estado</div></div>
        <div style="display:flex;flex-direction:column">
            <?php
            $colores = [
                'pendiente_oferta'           => '#f5a623',
                'sin_oferta'                 => '#ff3d5a',
                'oferta_enviada'             => '#4da6ff',
                'derivado_comercializadora'  => '#a78bff',
                'contratado'                 => '#00e5a0',
                'rechazado_cliente'          => '#ff6b35',
                'rechazado_comercializadora' => '#cc2222',
            ];
            foreach ($chart_estados as $row):
                $color = $colores[$row['estado']] ?? '#888';
            ?>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
                <div style="display:flex;align-items:center;gap:10px">
                    <span style="width:10px;height:10px;border-radius:50%;background:<?= $color ?>;flex-shrink:0;display:inline-block"></span>
                    <span style="font-size:0.83rem;color:var(--text)"><?= str_replace('_', ' ', $row['estado']) ?></span>
                </div>
                <span style="font-family:var(--mono);font-size:0.9rem;color:<?= $color ?>;font-weight:600">
                    <?= $row['total'] ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><div class="card-title">Top comercializadoras actuales</div></div>
        <div class="chart-container"><canvas id="chartComs"></canvas></div>
    </div>
</div>

<!-- Top ahorros -->
<div class="card">
    <div class="card-header"><div class="card-title">Top 5 — Mayor ahorro mensual potencial</div></div>
    <?php if ($ahorro_top): ?>
    <div style="display:flex;flex-direction:column">
        <?php foreach ($ahorro_top as $row): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
            <div>
                <div style="font-size:0.85rem;font-weight:500;color:var(--text)">
                    <?= htmlspecialchars($row['nombre_cliente'] ?: '—') ?>
                </div>
                <div style="font-size:0.72rem;color:var(--text3);margin-top:1px">
                    → <?= htmlspecialchars($row['comercializadora_ofertada'] ?: '—') ?>
                </div>
            </div>
            <div style="text-align:right">
                <div style="font-family:var(--mono);font-size:1rem;color:var(--green);font-weight:700">
                    <?= number_format($row['ahorro_mensual'], 2, ',', '.') ?>€
                </div>
                <div style="font-size:0.67rem;color:var(--text3)">/ mes</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="color:var(--text3);font-size:0.82rem;padding:20px 0;text-align:center">Sin datos de ahorro aún</p>
    <?php endif; ?>
</div>

<script>
const _ch = {
    plugins: { legend: { labels: { color:'#555555', font:{ family:'Inter', size:11 } } } },
    scales: {
        x: { ticks:{ color:'#555555', font:{ size:11 } }, grid:{ color:'#1c1c1c' } },
        y: { ticks:{ color:'#555555', font:{ size:11 } }, grid:{ color:'#1c1c1c' } }
    }
};

// Facturas por día
const diasData = <?= json_encode(array_values($chart_dias)) ?>;
new Chart(document.getElementById('chartDias'), {
    type: 'bar',
    data: {
        labels: diasData.map(d => {
            const [,mes,dia] = d.dia.split('-');
            return `${dia}/${mes}`;
        }),
        datasets: [
            { label:'Total',       data: diasData.map(d => d.total),
              backgroundColor:'rgba(0,229,160,0.18)', borderColor:'#00e5a0', borderWidth:1.5, borderRadius:3 },
            { label:'Contratados', data: diasData.map(d => d.contratados),
              backgroundColor:'rgba(0,212,232,0.18)', borderColor:'#00d4e8', borderWidth:1.5, borderRadius:3 }
        ]
    },
    options: { ...structuredClone(_ch), responsive:true, maintainAspectRatio:false }
});

// Distribución por estado
const estData = <?= json_encode(array_values($chart_estados)) ?>;
const estColors = {
    pendiente_oferta:          '#f5a623',
    sin_oferta:                '#ff3d5a',
    oferta_enviada:            '#4da6ff',
    derivado_comercializadora: '#a78bff',
    contratado:                '#00e5a0',
    rechazado_cliente:         '#ff6b35',
    rechazado_comercializadora:'#cc2222'
};
new Chart(document.getElementById('chartEstados'), {
    type: 'doughnut',
    data: {
        labels: estData.map(d => d.estado.replace(/_/g,' ')),
        datasets: [{
            data: estData.map(d => d.total),
            backgroundColor: estData.map(d => estColors[d.estado] || '#a78bff'),
            borderColor: '#0f0f0f', borderWidth: 3
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position:'right', labels:{ color:'#555555', font:{family:'Inter',size:11}, padding:12, boxWidth:11 } } }
    }
});

// Top comercializadoras
const comData = <?= json_encode(array_values($chart_coms)) ?>;
new Chart(document.getElementById('chartComs'), {
    type: 'bar',
    data: {
        labels: comData.map(d => d.comercializadora_actual),
        datasets: [{
            label: 'Facturas', data: comData.map(d => d.clientes),
            backgroundColor:'rgba(0,212,232,0.18)', borderColor:'#00d4e8', borderWidth:1.5, borderRadius:3
        }]
    },
    options: { ...structuredClone(_ch), responsive:true, maintainAspectRatio:false, indexAxis:'y' }
});
</script>