<?php
// ============================================================
// InvoiceOptimizer Ai — Login
// ============================================================
require __DIR__ . '/../config.php';
require __DIR__ . '/../db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Si ya está logueado, redirige al panel
if (isset($_SESSION['usuario_id'])) {
    header('Location: /index.php');
    exit;
}

$error = '';
$intentos_key = 'login_intentos_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Protección básica contra fuerza bruta
    $intentos = $_SESSION[$intentos_key] ?? 0;
    if ($intentos >= 5) {
        $error = 'Demasiados intentos fallidos. Cierra el navegador e inténtalo de nuevo.';
    } elseif (!$email || !$password) {
        $error = 'Introduce email y contraseña.';
    } else {
        $usuario = q1($pdo, "SELECT * FROM usuarios WHERE email = ? AND activo = 1", [$email]);

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Login correcto — creamos sesión
            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email']  = $usuario['email'];
            $_SESSION['usuario_rol']    = $usuario['rol'];
            unset($_SESSION[$intentos_key]);

            header('Location: /index.php');
            exit;
        } else {
            // Login incorrecto — sumamos intento
            $_SESSION[$intentos_key] = $intentos + 1;
            $error = 'Email o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>InvoiceOptimizer Ai · Acceder</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', 'Segoe UI', sans-serif; background: #0d0f14; color: #e8eaf0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
.box { background: #13161e; border: 1px solid #242835; border-radius: 12px; padding: 40px; width: 100%; max-width: 380px; }
.brand { font-size: 1.3rem; font-weight: 700; margin-bottom: 4px; }
.brand span { color: #6c63ff; }
.sub { font-size: 0.78rem; color: #555b72; margin-bottom: 32px; text-transform: uppercase; letter-spacing: 0.8px; }
label { display: block; font-size: 0.75rem; color: #8b90a7; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
input { width: 100%; background: #1a1e29; border: 1px solid #2e3347; border-radius: 8px; color: #e8eaf0; padding: 11px 14px; font-size: 0.9rem; margin-bottom: 20px; outline: none; transition: border-color 0.15s; }
input:focus { border-color: #6c63ff; }
button { width: 100%; background: #6c63ff; color: #fff; border: none; border-radius: 8px; padding: 12px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.15s; }
button:hover { background: #8b85ff; }
.error { padding: 10px 14px; border-radius: 8px; font-size: 0.83rem; margin-bottom: 20px; background: #2a0f0f; color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
</style>
</head>
<body>
<div class="box">
  <div class="brand">Invoice<span>Optimizer AI</span></div>
  <div class="sub">Panel de Administración</div>

  <?php if ($error): ?>
  <div class="error">⚠ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>Email</label>
    <input type="email" name="email" placeholder="tu@email.com" required autofocus
           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    <label>Contraseña</label>
    <input type="password" name="password" placeholder="••••••••" required>
    <button type="submit">Acceder</button>
  </form>
</div>
</body>
</html>
