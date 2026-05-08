<?php
ob_start();
require __DIR__ . '/config.php';
require __DIR__ . '/db.php';
require __DIR__ . '/auth/auth.php';

$action  = $_GET['action'] ?? 'dashboard';
$allowed = ['dashboard', 'clientes', 'cliente', 'factura', 'ofertas', 'errores', 'usuarios', 'perfil', 'comercializadoras','avisos'];
if (!in_array($action, $allowed)) $action = 'dashboard';
if ($action === 'usuarios' && !es_admin()) $action = 'dashboard';

$msg = $_GET['msg'] ?? '';

$page_css = [
    'dashboard' => '/css/dashboard.css',
    'clientes'  => '/css/clientes.css',
    'cliente'   => '/css/cliente.css',
    'ofertas'   => '/css/ofertas.css',
    'factura' => '/css/factura.css',
    'usuarios' => '/css/usuarios.css',
    'perfil'   => '/css/usuarios.css',
    'comercializadoras' => '/css/comercializadoras.css',
    'avisos' => '/css/avisos.css'
];

// ── Acciones POST globales ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_estado_factura']) && es_admin()) {
    $fid    = (int)$_POST['factura_id'];
    $estado = $_POST['nuevo_estado'];
    // Estados válidos según enum real de la DB
    $allowed_estados = [
        'pendiente_oferta',
        'sin_oferta',
        'oferta_enviada',
        'derivado_comercializadora',
        'contratado',
        'rechazado_cliente',
        'rechazado_comercializadora',
    ];
    if (in_array($estado, $allowed_estados) && $pdo) {
        $pdo->prepare("UPDATE facturas SET estado = ? WHERE id = ?")->execute([$estado, $fid]);
    }
    header("Location: ?action=clientes&msg=" . urlencode('Estado actualizado.'));
    exit;
}
// Cambio estado cliente (activo/inactivo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_estado_cliente']) && es_admin()) {
    $cid    = (int)$_POST['cliente_id'];
    $estado = $_POST['nuevo_estado_cliente'];
    $allowed_estados_cliente = ['lead', 'activo', 'inactivo'];
    if (in_array($estado, $allowed_estados_cliente) && $pdo) {
        $pdo->prepare("UPDATE clientes SET estado = ? WHERE id = ?")->execute([$estado, $cid]);
    }
    header("Location: ?action=cliente&id=$cid");
    exit;
}

// ── Badge: contador de avisos pendientes ───────────────────
$avisos_count = 0;
if ($pdo) {
    require_once __DIR__ . '/queries/seguimientos.queries.php';
    try {
        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM seguimientos s
            WHERE s.estado = 'activo'
              AND (s.bloqueado_hasta IS NULL OR s.bloqueado_hasta < CURDATE())
              AND s.fecha_proximo_contacto <= CURDATE()
        ");
        $avisos_count = (int) $stmt->fetchColumn();
    } catch (Exception $e) {
        $avisos_count = 0;
    }
}

$titles = [
    'dashboard' => 'Dashboard',
    'clientes'  => 'Gestión de Clientes',
    'cliente'   => 'Ficha de Cliente',
    'ofertas'   => 'Gestión de Ofertas',
    'errores'   => 'Log de Errores',
    'usuarios'  => 'Gestión de Usuarios',
    'perfil'    => 'Mi Perfil',
    'factura' => 'Detalle de Factura',
    'comercializadoras' => 'Gestión de Comercializadoras',
    'avisos' => 'Avisos de Renovación'

];

$nav = [
    ['action' => 'dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard',    'section' => 'Principal'],
    ['action' => 'clientes',  'icon' => 'users',             'label' => 'Clientes',      'section' => null],
    ['action' => 'ofertas',   'icon' => 'zap',               'label' => 'Ofertas',        'section' => 'Configuración'],
    ['action' => 'avisos', 'icon' => 'bell', 'label' => 'Avisos', 'section' => null],
    ['action' => 'comercializadoras', 'icon' => 'building-2', 'label' => 'Comercializadoras', 'section' => null],
    ['action' => 'errores',   'icon' => 'alert-triangle',    'label' => 'Log de errores', 'section' => null],
];
if (es_admin()) {
    $nav[] = ['action' => 'usuarios', 'icon' => 'shield', 'label' => 'Usuarios', 'section' => 'Administración'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>InvoiceOptimizer Ai · Panel de Administración</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/styles.css">
<link rel="stylesheet" href="/css/sidebar-collapsed.css">
<?php if (isset($page_css[$action])): ?>
<link rel="stylesheet" href="<?= $page_css[$action] ?>">
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
(function() {
    const t = localStorage.getItem('optitech_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
    if (localStorage.getItem('sidebar_collapsed') === 'true') {
        document.documentElement.classList.add('sidebar-pre-collapsed');
    }
})();
</script>
</head>
<body>

<div class="sidebar-overlay" id="sidebar-overlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="?action=dashboard" style="text-decoration:none;flex:1">
            <div class="brand">Invoice<span>Optimizer AI</span></div>
            <div class="sub">Panel de Administración</div>
        </a>
        <button class="btn-sidebar-toggle" id="btn-toggle-sidebar" title="Colapsar">
            <i data-lucide="panel-left-close"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <?php
        $last_section = null;
        foreach ($nav as $item):
            if ($item['section'] && $item['section'] !== $last_section):
                $last_section = $item['section'];
        ?>
        <div class="nav-section"><?= $item['section'] ?></div>
        <?php endif;
            $is_active = ($action === $item['action']) ||
                         ($item['action'] === 'clientes' && $action === 'cliente');
            $is_avisos = $item['action'] === 'avisos';
            $tooltip   = $item['label'] . ($is_avisos && $avisos_count > 0 ? ' (' . $avisos_count . ')' : '');
        ?>
        <a href="?action=<?= $item['action'] ?>"
           class="nav-item <?= $is_active ? 'active' : '' ?>"
           data-tooltip="<?= $tooltip ?>">
            <span class="icon">
                <i data-lucide="<?= $item['icon'] ?>"></i>
                <?php if ($is_avisos && $avisos_count > 0): ?>
                <span class="nav-badge"><?= $avisos_count > 99 ? '99+' : $avisos_count ?></span>
                <?php endif; ?>
            </span>
            <span class="nav-label"><?= $item['label'] ?></span>
            <?php if ($is_avisos && $avisos_count > 0): ?>
            <span class="nav-badge-inline"><?= $avisos_count > 99 ? '99+' : $avisos_count ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>

        <div class="nav-section">Mi cuenta</div>
        <a href="?action=perfil"
           class="nav-item <?= $action==='perfil' ? 'active' : '' ?>"
           data-tooltip="Mi perfil">
            <span class="icon"><i data-lucide="user"></i></span>
            <span class="nav-label">Mi perfil</span>
        </a>
    </nav>

    <div class="sidebar-user">
        <a href="?action=perfil" style="text-decoration:none;display:block;margin-bottom:9px">
            <div class="user-name"><?= htmlspecialchars($usuario_actual['nombre']) ?></div>
            <div class="user-role"><?= ucfirst($usuario_actual['rol']) ?> · <?= htmlspecialchars($usuario_actual['email']) ?></div>
        </a>
        <a href="/auth/logout.php" style="text-decoration:none;display:block">
            <button class="btn-logout">
                <i data-lucide="log-out" style="width:13px;height:13px"></i>
                <span>Cerrar sesión</span>
            </button>
        </a>
    </div>
</aside>

<div class="main" id="main">
    <header class="topbar">
        <button class="btn-hamburger" id="btn-hamburger" aria-label="Menú">
            <i data-lucide="menu"></i>
        </button>
        <div class="topbar-title"><?= $titles[$action] ?? 'Panel' ?></div>
        <button class="btn-theme-toggle" id="btn-theme" title="Cambiar tema">
            <span class="icon-moon">🌙</span>
            <span class="icon-sun">☀️</span>
        </button>
    </header>

    <div class="content" id="content">
        <?php require __DIR__ . "/pages/$action.php"; ?>
    </div>
</div>

<div class="toast-container">
    <?php if (!empty($db_error)): ?>
    <div class="alert alert-error">⚠ Error de conexión: <?= htmlspecialchars($db_error) ?></div>
    <?php endif; ?>
    <?php if ($msg): ?>
    <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
</div>

<script src="/js/alerts.js"></script>
<script src="/js/sidebar.js"></script>
<script>
lucide.createIcons();


document.getElementById('btn-theme').addEventListener('click', () => {
    const html = document.documentElement;
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('optitech_theme', next);
});
</script>
</body>
</html>
<?php ob_end_flush(); ?>