<?php
/**
 * InvoiceOptimizer Ai — Queries del dashboard
 */

function q_dashboard_kpis(PDO $pdo): array {
    return q1($pdo, "
        SELECT
            (SELECT COUNT(*) FROM clientes) AS total_clientes,

            (SELECT COUNT(*) FROM facturas) AS total_facturas,

            (SELECT COUNT(*) FROM facturas WHERE estado = 'contratado') AS contratados,

            (SELECT ROUND(SUM(ahorro_mensual),2) FROM facturas
             WHERE estado = 'contratado') AS ahorro_confirmado,

            (SELECT ROUND(SUM(ahorro_mensual),2) FROM facturas
             WHERE estado IN ('oferta_enviada','derivado_comercializadora')) AS ahorro_pipeline,

            (SELECT ROUND(SUM(COALESCE(comision_mensual,0)),2) FROM facturas) AS comision_total

    ") ?? [];
}
function q_dashboard_chart_dias(PDO $pdo): array {
    return q($pdo, "
        SELECT DATE(f.timestamp_entrada) AS dia,
               COUNT(*) AS total,
               SUM(CASE WHEN f.estado = 'contratado' THEN 1 ELSE 0 END) AS contratados
        FROM facturas f
        WHERE f.timestamp_entrada >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(f.timestamp_entrada)
        ORDER BY dia ASC
    ");
}

function q_dashboard_chart_estados(PDO $pdo): array {
    return q($pdo, "
        SELECT estado, COUNT(*) AS total
        FROM facturas
        GROUP BY estado
        ORDER BY total DESC
    ");
}

function q_dashboard_chart_comercializadoras(PDO $pdo): array {
    return q($pdo, "
        SELECT comercializadora_actual,
               COUNT(*) AS clientes,
               ROUND(AVG(importe_total), 2) AS factura_media
        FROM facturas
        WHERE comercializadora_actual IS NOT NULL AND comercializadora_actual != ''
        GROUP BY comercializadora_actual
        ORDER BY clientes DESC
        LIMIT 8
    ");
}

function q_dashboard_ahorro_top(PDO $pdo): array {
    return q($pdo, "
        SELECT c.nombre_cliente, f.ahorro_mensual,
               f.comercializadora_ofertada, f.estado
        FROM facturas f
        JOIN clientes c ON c.id = f.cliente_id
        WHERE f.ahorro_mensual > 0
        AND f.estado IN ('oferta_enviada','derivado_comercializadora','contratado')
        ORDER BY f.ahorro_mensual DESC
        LIMIT 5
    ");
}

function q_dashboard_comision_semanal(PDO $pdo): array {
    return q($pdo, "
        SELECT YEARWEEK(f.timestamp_entrada, 1) AS semana,
               ROUND(SUM(f.comision_mensual), 2) AS comision,
               COUNT(*) AS contratos
        FROM facturas f
        WHERE f.estado = 'contratado'
          AND f.timestamp_entrada >= DATE_SUB(NOW(), INTERVAL 12 WEEK)
        GROUP BY semana
        ORDER BY semana ASC
    ");
}
function q_dashboard_acciones(PDO $pdo): array {
    return q($pdo, "
        SELECT 
            f.id,
            c.nombre_cliente,
            f.estado,
            f.timestamp_entrada,
            f.fecha_oferta_enviada
        FROM facturas f
        JOIN clientes c ON c.id = f.cliente_id
        WHERE f.estado IN ('pendiente_oferta','oferta_enviada','revision_manual')
        ORDER BY 
            CASE 
                WHEN f.estado = 'pendiente_oferta' THEN 1
                WHEN f.estado = 'revision_manual' THEN 2
                WHEN f.estado = 'oferta_enviada' THEN 3
            END,
            f.timestamp_entrada ASC
        LIMIT 10
    ");
}