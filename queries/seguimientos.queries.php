<?php

function q_config_seguimientos(PDO $pdo): array {
    $r = $pdo->query("SELECT * FROM config_seguimientos WHERE id=1")->fetch(PDO::FETCH_ASSOC);

    return $r ?: [
        'dias_recontacto_inicial' => 330,
        'dias_segundo_contacto'   => 30,
        'bloqueo_dias'            => 1
    ];
}

function q_config_seguimientos_update(PDO $pdo, int $inicial, int $segundo, int $bloqueo): void {
    $pdo->prepare("
        UPDATE config_seguimientos 
        SET dias_recontacto_inicial=?, dias_segundo_contacto=?, bloqueo_dias=?
        WHERE id=1
    ")->execute([$inicial, $segundo, $bloqueo]);
}


/**
 * Avisos activos (solo lo necesario)
 */
function q_avisos_activos(PDO $pdo): array {

    $stmt = $pdo->prepare("
        SELECT 
            base.seg_id,
            base.fecha_inicio,
            base.fecha_proximo_contacto,
            base.seg_estado,
            base.retraso,

            /* PRIORIDAD */
            CASE
                WHEN base.retraso <= 6 THEN 1
                WHEN base.retraso <= 15 THEN 2
                WHEN base.retraso <= 30 THEN 3
                ELSE 4
            END AS prioridad,

            /* ESTADO */
            CASE
                WHEN base.retraso = 0 THEN 'Hoy'
                WHEN base.retraso <= 6 THEN 'Reciente'
                WHEN base.retraso <= 15 THEN 'Urgente'
                WHEN base.retraso <= 30 THEN 'Crítico'
                ELSE 'Muy crítico'
            END AS estado_label,

            base.cliente_id,
            base.nombre_cliente,
            base.email_cliente,
            base.cups,
            base.comercializadora_actual,
            base.tarifa,
            base.ahorro_mensual,
            base.nombre_oferta

        FROM (
            SELECT 
                s.id AS seg_id,
                s.fecha_inicio,
                s.fecha_proximo_contacto,
                s.estado AS seg_estado,

                /* 👉 cálculo UNA SOLA VEZ */
                GREATEST(DATEDIFF(CURDATE(), s.fecha_proximo_contacto), 0) AS retraso,

                c.id AS cliente_id,
                c.nombre_cliente,
                c.email_cliente,

                f.cups,
                f.comercializadora_actual,
                f.tarifa,
                f.ahorro_mensual,
                o.nombre_oferta

            FROM seguimientos s
            JOIN clientes c ON c.id = s.cliente_id
            JOIN facturas f ON f.id = s.factura_id
            LEFT JOIN ofertas o ON o.id = f.oferta_asignada
            WHERE s.estado = 'activo'
              AND (s.bloqueado_hasta IS NULL OR s.bloqueado_hasta < CURDATE())
              AND s.fecha_proximo_contacto <= CURDATE()
        ) base

        ORDER BY prioridad ASC, retraso DESC
    ");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Posponer seguimiento (SOLO 1 cliente)
 */
function q_seguimiento_posponer(PDO $pdo, int $id, int $bloqueo_dias, int $dias_segundo): void {
    $pdo->prepare("
        UPDATE seguimientos SET
            bloqueado_hasta = DATE_ADD(NOW(), INTERVAL ? DAY),
            fecha_proximo_contacto = DATE_ADD(NOW(), INTERVAL ? DAY)
        WHERE id=?
    ")->execute([$bloqueo_dias, $dias_segundo, $id]);
}


/**
 * Cerrar seguimiento
 */
function q_seguimiento_cerrar(PDO $pdo, int $id): void {
    $pdo->prepare("
        UPDATE seguimientos 
        SET estado='cerrado' 
        WHERE id=?
    ")->execute([$id]);
}

/**
 * Contador de avisos pendientes para el badge del nav
 */
function q_avisos_pendientes_count(PDO $pdo): int {
    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM seguimientos s
        WHERE s.estado = 'activo'
          AND (s.bloqueado_hasta IS NULL OR s.bloqueado_hasta < CURDATE())
          AND s.fecha_proximo_contacto <= CURDATE()
    ");
    return (int) $stmt->fetchColumn();
}