<?php
/**
 * Queries de ficha de factura individual
 */

function q_factura_by_id(PDO $pdo, int $id): ?array {
    return q1($pdo, "SELECT * FROM facturas WHERE id = ?", [$id]) ?: null;
}

function q_factura_oferta(PDO $pdo, int $oferta_id): ?array {
    return q1($pdo, "SELECT * FROM ofertas WHERE id = ?", [$oferta_id]) ?: null;
}

function q_factura_cliente(PDO $pdo, int $cliente_id): ?array {
    return q1($pdo, "SELECT id, nombre_cliente, email_cliente, telefono_cliente FROM clientes WHERE id = ?", [$cliente_id]) ?: null;
}
