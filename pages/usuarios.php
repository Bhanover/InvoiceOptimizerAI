<?php
require_once __DIR__ . '/../queries/usuarios.queries.php';

if (!es_admin()) {
    echo '<div class="alert alert-error">⚠ No tienes permisos para acceder a esta sección.</div>';
    return;
}

$error_inline = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['crear_usuario'])) {
        $nombre   = trim($_POST['nombre']   ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $rol      = $_POST['rol']           ?? 'viewer';

        if (!$nombre || !$email || !$password) {
            $error_inline = 'Todos los campos son obligatorios.';
        } elseif (strlen($password) < 6) {
            $error_inline = 'La contraseña debe tener al menos 6 caracteres.';
        } elseif (q_usuario_email_existe($pdo, $email)) {
            $error_inline = 'Ya existe un usuario con ese email.';
        } else {
            q_usuario_crear($pdo, $nombre, $email, password_hash($password, PASSWORD_BCRYPT), $rol);
            header("Location: ?action=usuarios&msg=" . urlencode('Usuario creado correctamente.'));
            exit;
        }
    }

    if (isset($_POST['toggle_usuario'])) {
        $id = (int)$_POST['usuario_id'];
        if ($id === (int)$usuario_actual['id']) {
            header("Location: ?action=usuarios&msg=" . urlencode('No puedes desactivarte a ti mismo.'));
            exit;
        }
        q_usuario_toggle_activo($pdo, $id, (int)$_POST['activo']);
        header("Location: ?action=usuarios&msg=" . urlencode('Usuario actualizado.'));
        exit;
    }

    if (isset($_POST['editar_usuario'])) {
        $id           = (int)$_POST['usuario_id'];
        $rol          = $_POST['rol']          ?? 'viewer';
        $new_password = $_POST['new_password'] ?? '';

        if ($new_password && strlen($new_password) < 6) {
            header("Location: ?action=usuarios&msg=" . urlencode('La contraseña debe tener al menos 6 caracteres.'));
            exit;
        }
        $hash = $new_password ? password_hash($new_password, PASSWORD_BCRYPT) : null;
        q_usuario_editar($pdo, $id, $rol, $hash);
        header("Location: ?action=usuarios&msg=" . urlencode('Usuario actualizado correctamente.'));
        exit;
    }
}

$usuarios = q_usuarios_all($pdo);
?>

<div class="section-title">Gestión de usuarios</div>
<div class="section-sub"><?= count($usuarios) ?> usuarios registrados</div>

<?php if ($error_inline): ?>
<div class="alert alert-error"><?= htmlspecialchars($error_inline) ?></div>
<?php endif; ?>

<!-- Crear usuario -->
<div class="card" style="margin-bottom:22px">
    <div class="card-header"><div class="card-title">Crear nuevo usuario</div></div>
    <form method="POST" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
        <input type="hidden" name="crear_usuario" value="1">
        <div>
            <label class="form-label">Nombre</label>
            <input type="text"  name="nombre"   class="form-input" placeholder="Nombre completo" required style="width:175px;margin-bottom:0">
        </div>
        <div>
            <label class="form-label">Email</label>
            <input type="email" name="email"    class="form-input" placeholder="email@InvoiceOptimizer.com" required style="width:195px;margin-bottom:0">
        </div>
        <div>
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-input" placeholder="Mínimo 6 caracteres" required style="width:175px;margin-bottom:0">
        </div>
        <div>
            <label class="form-label">Rol</label>
            <select name="rol" class="form-input" style="width:115px;margin-bottom:0">
                <option value="viewer">Viewer</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear usuario</button>
    </form>
</div>

<!-- Tabla -->
<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th>
                    <th>Estado</th><th>Creado</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td class="mono">#<?= $u['id'] ?></td>
                <td class="primary">
                    <?= htmlspecialchars($u['nombre']) ?>
                    <?php if ($u['id'] == $usuario_actual['id']): ?>
                    <span style="font-size:0.68rem;color:var(--green);margin-left:4px">(tú)</span>
                    <?php endif; ?>
                </td>
                <td style="color:var(--text2)"><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <span class="badge <?= $u['rol']==='admin'?'badge-contratado':'badge-enviada' ?>">
                        <?= ucfirst($u['rol']) ?>
                    </span>
                </td>
                <td>
                    <span class="badge <?= $u['activo']?'badge-contratado':'badge-descartado' ?>">
                        <?= $u['activo']?'Activo':'Inactivo' ?>
                    </span>
                </td>
                <td style="font-size:0.73rem;color:var(--text3)"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                <td style="display:flex;gap:6px;align-items:center">
                    <button class="btn btn-sm btn-ghost"
                            onclick="abrirModalEditarUsuario(<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>)">
                        ✏ Editar
                    </button>
                    <?php if ($u['id'] != $usuario_actual['id']): ?>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                        <input type="hidden" name="activo"     value="<?= $u['activo'] ?>">
                        <button type="submit" name="toggle_usuario"
                                class="btn btn-sm <?= $u['activo']?'btn-danger':'btn-success' ?>">
                            <?= $u['activo']?'Desactivar':'Activar' ?>
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../modals/usuario-modal.php'; ?>
<script src="/js/usuarios.js"></script>
