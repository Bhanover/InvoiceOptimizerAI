<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';
if ($api_key !== API_IMPORT_KEY) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$body    = file_get_contents('php://input');
$ofertas = json_decode($body, true);

if (!is_array($ofertas) || empty($ofertas)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'JSON inválido o vacío']);
    exit;
}

$limpias = [];
foreach ($ofertas as $o) {
    if (empty($o['nombre_oferta']) || empty($o['comercializadora'])) continue;

    // round() a 6 decimales preserva la precisión real del PDF
    $r6 = fn($v) => round((float)($v ?? 0), 6);

    $limpias[] = [
        'nombre_oferta'      => trim($o['nombre_oferta']),
        'comercializadora'   => trim($o['comercializadora']),
        'version_tarifa'     => trim($o['version_tarifa']     ?? ''),
        'tipo_tarifa'        => trim($o['tipo_tarifa']        ?? ''),
        'precio_energia_p1'  => $r6($o['precio_energia_p1']),
        'precio_energia_p2'  => $r6($o['precio_energia_p2']),
        'precio_energia_p3'  => $r6($o['precio_energia_p3']),
        'precio_energia_p4'  => $r6($o['precio_energia_p4']),
        'precio_energia_p5'  => $r6($o['precio_energia_p5']),
        'precio_energia_p6'  => $r6($o['precio_energia_p6']),
        'precio_potencia_p1' => $r6($o['precio_potencia_p1']),
        'precio_potencia_p2' => $r6($o['precio_potencia_p2']),
        'precio_potencia_p3' => $r6($o['precio_potencia_p3']),
        'precio_potencia_p4' => $r6($o['precio_potencia_p4']),
        'precio_potencia_p5' => $r6($o['precio_potencia_p5']),
        'precio_potencia_p6' => $r6($o['precio_potencia_p6']),
        'fee_minimo'         => $r6($o['fee_minimo']),
        'fee_maximo'         => $r6($o['fee_maximo']),
        'activa'             => 1,
    ];
}

echo json_encode(['ok' => true, 'ofertas' => $limpias]);
