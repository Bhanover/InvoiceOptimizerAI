<?php
// ============================================================
// ENERGÍA — Conexión a base de datos
// ============================================================
$pdo = null;
$db_error = null;
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::MYSQL_ATTR_SSL_CA              => __DIR__ . '/ca.pem',
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

function q($pdo, $sql, $params = []) {
    if (!$pdo) return [];
    $st = $pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function q1($pdo, $sql, $params = []) {
    $r = q($pdo, $sql, $params);
    return $r ? $r[0] : [];
}
