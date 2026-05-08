<?php
/**
 * InvoiceOptimizer Ai — Queries de comercializadoras
 */

function q_comercializadoras_all(PDO $pdo): array {
    return q($pdo, "SELECT * FROM comercializadoras ORDER BY activa DESC, nombre ASC");
}

function q_comercializadora_crear(PDO $pdo, array $d): void {
    $pdo->prepare("
        INSERT INTO comercializadoras (nombre, email_contacto, telefono, direccion, activa)
        VALUES (?, ?, ?, ?, ?)
    ")->execute([
        $d['nombre'],
        $d['email_contacto'] ?? '',
        $d['telefono']       ?? '',
        $d['direccion']      ?? '',
        $d['activa']         ?? 1,
    ]);
}

function q_comercializadora_editar(PDO $pdo, int $id, array $d): void {
    $pdo->prepare("
        UPDATE comercializadoras SET
            nombre         = ?,
            email_contacto = ?,
            telefono       = ?,
            direccion      = ?,
            activa         = ?
        WHERE id = ?
    ")->execute([
        $d['nombre'],
        $d['email_contacto'] ?? '',
        $d['telefono']       ?? '',
        $d['direccion']      ?? '',
        $d['activa']         ?? 1,
        $id,
    ]);
}

function q_comercializadora_toggle(PDO $pdo, int $id, int $activa): void {
    $pdo->prepare("UPDATE comercializadoras SET activa = ? WHERE id = ?")
        ->execute([$activa, $id]);
}

function q_comercializadora_eliminar(PDO $pdo, int $id): void {
    $pdo->prepare("DELETE FROM comercializadoras WHERE id = ?")
        ->execute([$id]);
}
