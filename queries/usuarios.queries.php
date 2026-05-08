<?php
/**
 * InvoiceOptimizer Ai — Queries de usuarios
 */

function q_usuarios_all(PDO $pdo): array {
    return q($pdo, "SELECT id, nombre, email, rol, activo, created_at FROM usuarios ORDER BY created_at ASC");
}

function q_usuario_email_existe(PDO $pdo, string $email): bool {
    return (int)(q1($pdo, "SELECT COUNT(*) AS n FROM usuarios WHERE email = ?", [$email])['n'] ?? 0) > 0;
}

function q_usuario_crear(PDO $pdo, string $nombre, string $email, string $hash, string $rol): void {
    $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)")
        ->execute([$nombre, $email, $hash, $rol]);
}

function q_usuario_toggle_activo(PDO $pdo, int $id, int $activo_actual): void {
    $pdo->prepare("UPDATE usuarios SET activo = ? WHERE id = ?")->execute([$activo_actual ? 0 : 1, $id]);
}

function q_usuario_editar(PDO $pdo, int $id, string $rol, ?string $hash): void {
    if ($hash) {
        $pdo->prepare("UPDATE usuarios SET rol = ?, password = ? WHERE id = ?")->execute([$rol, $hash, $id]);
    } else {
        $pdo->prepare("UPDATE usuarios SET rol = ? WHERE id = ?")->execute([$rol, $id]);
    }
}

function q_usuario_by_id(PDO $pdo, int $id): ?array {
    return q1($pdo, "SELECT * FROM usuarios WHERE id = ?", [$id]) ?: null;
}

function q_usuario_cambiar_password(PDO $pdo, int $id, string $hash): void {
    $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?")->execute([$hash, $id]);
}
