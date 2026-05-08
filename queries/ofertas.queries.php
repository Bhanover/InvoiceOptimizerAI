<?php
/**
 * InvoiceOptimizer Ai — Queries de ofertas
 */

function q_ofertas_all(PDO $pdo): array {
    return q($pdo, "SELECT * FROM ofertas ORDER BY activa DESC, id ASC");
}

function q_oferta_crear(PDO $pdo, array $d): void {
    $pdo->prepare("
        INSERT INTO ofertas
            (nombre_oferta, comercializadora, email_comercializadora,
             precio_energia_p1, precio_energia_p2, precio_energia_p3, precio_energia_p4, precio_energia_p5, precio_energia_p6,
             precio_potencia_p1, precio_potencia_p2, precio_potencia_p3, precio_potencia_p4, precio_potencia_p5, precio_potencia_p6,
             fee_minimo, fee_maximo, version_tarifa, tipo_tarifa, activa)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ")->execute([
        $d['nombre_oferta'],
        $d['comercializadora'],
        $d['email_comercializadora'] ?? '',
        $d['precio_energia_p1']  ?? 0,
        $d['precio_energia_p2']  ?? 0,
        $d['precio_energia_p3']  ?? 0,
        $d['precio_energia_p4']  ?? 0,
        $d['precio_energia_p5']  ?? 0,
        $d['precio_energia_p6']  ?? 0,
        $d['precio_potencia_p1'] ?? 0,
        $d['precio_potencia_p2'] ?? 0,
        $d['precio_potencia_p3'] ?? 0,
        $d['precio_potencia_p4'] ?? 0,
        $d['precio_potencia_p5'] ?? 0,
        $d['precio_potencia_p6'] ?? 0,
        $d['fee_minimo'] ?? 0,
        $d['fee_maximo'] ?? 0,
        $d['version_tarifa'] ?? '',
        $d['tipo_tarifa']    ?? '',
        $d['activa']         ?? 1,
    ]);
}

function q_oferta_editar(PDO $pdo, int $id, array $d): void {
    $pdo->prepare("
        UPDATE ofertas SET
            nombre_oferta = ?, comercializadora = ?, email_comercializadora = ?,
            precio_energia_p1 = ?, precio_energia_p2 = ?, precio_energia_p3 = ?, precio_energia_p4 = ?, precio_energia_p5 = ?, precio_energia_p6 = ?,
            precio_potencia_p1 = ?, precio_potencia_p2 = ?, precio_potencia_p3 = ?, precio_potencia_p4 = ?, precio_potencia_p5 = ?, precio_potencia_p6 = ?,
            fee_minimo = ?, fee_maximo = ?, version_tarifa = ?, tipo_tarifa = ?, activa = ?
        WHERE id = ?
    ")->execute([
        $d['nombre_oferta'], $d['comercializadora'], $d['email_comercializadora'],
        $d['precio_energia_p1'], $d['precio_energia_p2'], $d['precio_energia_p3'], $d['precio_energia_p4'], $d['precio_energia_p5'], $d['precio_energia_p6'],
        $d['precio_potencia_p1'], $d['precio_potencia_p2'], $d['precio_potencia_p3'], $d['precio_potencia_p4'], $d['precio_potencia_p5'], $d['precio_potencia_p6'],
        $d['fee_minimo'], $d['fee_maximo'], $d['version_tarifa'], $d['tipo_tarifa'], $d['activa'],
        $id
    ]);
}

function q_oferta_toggle(PDO $pdo, int $id, int $nuevo_activa): void {
    $pdo->prepare("UPDATE ofertas SET activa = ? WHERE id = ?")->execute([$nuevo_activa, $id]);
}

function q_oferta_eliminar(PDO $pdo, int $id): void {
    $pdo->prepare("DELETE FROM ofertas WHERE id = ?")->execute([$id]);
}

function q_ofertas_insertar_bulk(PDO $pdo, array $ofertas): int {
    $insertadas = 0;
    foreach ($ofertas as $d) {
        q_oferta_crear($pdo, $d);
        $insertadas++;
    }
    return $insertadas;
}