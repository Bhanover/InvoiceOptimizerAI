<?php
/**
 * Queries de lista de clientes
 * Responsabilidad: listado, filtrado y conteo de clientes
 */

function q_clientes_lista(PDO $pdo, array $filtros, int $per_page, int $offset): array {
    [$where_sql, $params] = _clientes_where($filtros);

    return q($pdo,
        "SELECT id, nombre_cliente, email_cliente, telefono_cliente, estado, fecha_alta
            FROM clientes
            WHERE $where_sql
            ORDER BY id DESC
            LIMIT $per_page OFFSET $offset",
        $params
    );
}

function q_clientes_count(PDO $pdo, array $filtros): int {
    [$where_sql, $params] = _clientes_where($filtros);

    return (int)(q1($pdo,
        "SELECT COUNT(*) AS n FROM clientes WHERE $where_sql",
        $params
    )['n'] ?? 0);
}

/** @internal */
function _clientes_where(array $f): array {
    $where  = ['1=1'];
    $params = [];

    if (!empty($f['estado'])) {
        $where[]  = 'estado = ?';
        $params[] = $f['estado'];
    }

    if (!empty($f['buscar'])) {
        $like     = '%' . $f['buscar'] . '%';
        $where[]  = '(nombre_cliente LIKE ? OR email_cliente LIKE ? OR telefono_cliente LIKE ?)';
        $params   = array_merge($params, [$like, $like, $like]);
    }

    if (!empty($f['desde'])) { $where[] = 'DATE(fecha_alta) >= ?'; $params[] = $f['desde']; }
    if (!empty($f['hasta'])) { $where[] = 'DATE(fecha_alta) <= ?'; $params[] = $f['hasta']; }

    return [implode(' AND ', $where), $params];
}
