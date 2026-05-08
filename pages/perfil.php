<?php
require_once __DIR__ . '/../queries/usuarios.queries.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_password'])) {
    $actual  = $_POST['password_actual']  ?? '';
    $nuevo   = $_POST['password_nuevo']   ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    $usuario = q_usuario_by_id($pdo, $usuario_actual['id']);

    if (!password_verify($actual, $usuario['password'])) {
        header("Location: ?action=perfil&error=" . urlencode('La contraseña actual no es correcta.'));
        exit;
    }
    if (strlen($nuevo) < 6) {
        header("Location: ?action=perfil&error=" . urlencode('La nueva contraseña debe tener al menos 6 caracteres.'));
        exit;
    }
    if ($nuevo !== $confirm) {
        header("Location: ?action=perfil&error=" . urlencode('Las contraseñas nuevas no coinciden.'));
        exit;
    }

    q_usuario_cambiar_password($pdo, $usuario_actual['id'], password_hash($nuevo, PASSWORD_BCRYPT));
    header("Location: ?action=perfil&msg=" . urlencode('Contraseña actualizada correctamente.'));
    exit;
}

$error_perfil = $_GET['error'] ?? '';
$iniciales = strtoupper(mb_substr($usuario_actual['nombre'], 0, 1));
?>

<div class="section-title">Mi perfil</div>
<div class="section-sub">Gestiona tu información y contraseña</div>

<?php if ($error_perfil): ?>
<div class="alert alert-error" style="max-width:860px;margin-bottom:14px"><?= htmlspecialchars($error_perfil) ?></div>
<?php endif; ?>

<div class="perfil-grid">

    <!-- Información de la cuenta -->
    <div class="card">
        <div class="card-header"><div class="card-title">Información de la cuenta</div></div>
        <div class="perfil-avatar"><?= $iniciales ?></div>
        <div class="ficha-row">
            <span class="ficha-label">Nombre</span>
            <span class="ficha-val"><?= htmlspecialchars($usuario_actual['nombre']) ?></span>
        </div>
        <div class="ficha-row">
            <span class="ficha-label">Email</span>
            <span class="ficha-val" style="color:var(--text2);font-size:0.8rem"><?= htmlspecialchars($usuario_actual['email']) ?></span>
        </div>
        <div class="ficha-row">
            <span class="ficha-label">Rol</span>
            <span class="ficha-val">
                <span class="badge <?= $usuario_actual['rol']==='admin' ? 'badge-contratado' : 'badge-enviada' ?>">
                    <?= ucfirst($usuario_actual['rol']) ?>
                </span>
            </span>
        </div>
       
    </div>

    <!-- Cambiar contraseña -->
    <div class="card">
        <div class="card-header"><div class="card-title">Cambiar contraseña</div></div>
        <form method="POST" class="perfil-form">
            <input type="hidden" name="cambiar_password" value="1">
            <label class="form-label">Contraseña actual</label>
            <input type="password" name="password_actual"  class="form-input" required placeholder="••••••••">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" name="password_nuevo"   class="form-input" required placeholder="Mínimo 6 caracteres">
            <label class="form-label">Confirmar nueva contraseña</label>
            <input type="password" name="password_confirm" class="form-input" required placeholder="Repite la nueva contraseña">
            <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
        </form>
    </div>

</div>
