<?php
/**
 * Queries de ficha de cliente individual
 * Responsabilidad: datos detallados de un cliente concreto (facturas, mensajes)
 * La función q_cliente_by_id vive aquí porque es responsabilidad de esta página
 */

function q_cliente_by_id(PDO $pdo, int $id): ?array {
    return q1($pdo, "SELECT * FROM clientes WHERE id = ?", [$id]) ?: null;
}

function q_cliente_facturas(PDO $pdo, int $cliente_id): array {
    return q($pdo,
        "SELECT * FROM facturas WHERE cliente_id = ? ORDER BY timestamp_entrada DESC",
        [$cliente_id]
    );
}

function q_cliente_mensajes(PDO $pdo, int $cliente_id): array {
    return q($pdo,
        "SELECT * FROM mensajes WHERE cliente_id = ? ORDER BY timestamp_entrada DESC",
        [$cliente_id]
    );
}
