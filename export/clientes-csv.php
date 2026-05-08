<?php
/**
 * InvoiceOptimizer Ai — Exportación de clientes a CSV
 * URL: /export/clientes-csv.php?estado=contratado&desde=2026-01-01&hasta=2026-03-31
 */
require __DIR__ . '/../config.php';
require __DIR__ . '/../db.php';
require __DIR__ . '/../auth/auth.php';

// ── Filtros ───────────────────────────────────────────────
$filtro_estado = $_GET['estado'] ?? '';
$filtro_canal  = $_GET['canal']  ?? '';
$filtro_desde  = $_GET['desde']  ?? '';
$filtro_hasta  = $_GET['hasta']  ?? '';

$where  = [];
$params = [];

if ($filtro_estado) { $where[] = "estado = ?";              $params[] = $filtro_estado; }
if ($filtro_canal)  { $where[] = "canal = ?";               $params[] = $filtro_canal; }
if ($filtro_desde)  { $where[] = "DATE(fecha_alta) >= ?";   $params[] = $filtro_desde; }
if ($filtro_hasta)  { $where[] = "DATE(fecha_alta) <= ?";   $params[] = $filtro_hasta; }

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$clientes = q($pdo, "
    SELECT id, nombre_cliente, email_cliente, telefono_cliente, canal, cups,
           comercializadora_actual, comercializadora_nueva, tarifa,
           potencia_p1, potencia_p2, consumo_p1, consumo_p2,
           importe_total, ahorro_mensual, comision_mensual,
           estado, fecha_alta, fecha_oferta_enviada
    FROM clientes $where_sql
    ORDER BY fecha_alta DESC
", $params);

// ── Headers CSV ───────────────────────────────────────────
$filename = 'clientes_optitech_' . date('Y-m-d_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// BOM para Excel (compatibilidad con caracteres especiales)
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');

// ── Cabecera CSV ──────────────────────────────────────────
fputcsv($out, [
    'ID', 'Nombre', 'Email', 'Teléfono', 'Canal', 'CUPS',
    'Comercializadora actual', 'Comercializadora nueva', 'Tarifa',
    'Potencia P1 (kW)', 'Potencia P2 (kW)',
    'Consumo P1 (kWh)', 'Consumo P2 (kWh)',
    'Importe total (€)', 'Ahorro mensual (€)', 'Comisión mensual (€)',
    'Estado', 'Fecha alta', 'Fecha oferta enviada'
], ';');

// ── Filas ─────────────────────────────────────────────────
foreach ($clientes as $c) {
    fputcsv($out, [
        $c['id'],
        $c['nombre_cliente'],
        $c['email_cliente'],
        $c['telefono_cliente'],
        $c['canal'],
        $c['cups'],
        $c['comercializadora_actual'],
        $c['comercializadora_nueva'],
        $c['tarifa'],
        $c['potencia_p1'],
        $c['potencia_p2'],
        $c['consumo_p1'],
        $c['consumo_p2'],
        number_format($c['importe_total'] ?? 0, 2, ',', '.'),
        number_format($c['ahorro_mensual'] ?? 0, 2, ',', '.'),
        number_format($c['comision_mensual'] ?? 0, 2, ',', '.'),
        $c['estado'],
        $c['fecha_alta'] ? date('d/m/Y H:i', strtotime($c['fecha_alta'])) : '',
        $c['fecha_oferta_enviada'] ? date('d/m/Y H:i', strtotime($c['fecha_oferta_enviada'])) : '',
    ], ';');
}

fclose($out);
exit;
