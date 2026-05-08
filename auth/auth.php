<?php
// ============================================================
// InvoiceOptimizer Ai — Comprobación de autenticación
// Se incluye al inicio de index.php
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay sesión activa, redirige al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /auth/login.php');
    exit;
}

// Datos del usuario disponibles en todas las páginas
$usuario_actual = [
    'id'     => $_SESSION['usuario_id'],
    'nombre' => $_SESSION['usuario_nombre'],
    'email'  => $_SESSION['usuario_email'],
    'rol'    => $_SESSION['usuario_rol'],
];

// Helper para comprobar si es admin
function es_admin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}
