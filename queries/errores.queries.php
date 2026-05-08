<?php
/**
 * InvoiceOptimizer Ai — Queries de log de errores
 */

function q_errores_recientes(PDO $pdo, int $limit = 50): array {
    return q($pdo, "
        SELECT le.*,
               c.nombre_cliente AS nombre_cliente_rel,
               c.id             AS cliente_id_rel
        FROM log_errores le
        LEFT JOIN clientes c ON c.email_cliente = le.email_cliente
        ORDER BY le.created_at DESC
        LIMIT $limit
    ");
}
