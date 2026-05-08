<link rel="stylesheet" href="/css/ofertas.css">

<?php
/**
 * InvoiceOptimizer Ai — Gestión de comercializadoras
 */

require_once __DIR__ . '/../queries/comercializadoras.queries.php';

if (!$pdo) {
    echo "<div class='alert alert-error'>Error de conexión a la base de datos.</div>";
    exit;
}

function _com_from_post(): array {
    return [
        'nombre'         => trim($_POST['nombre']         ?? ''),
        'email_contacto' => trim($_POST['email_contacto'] ?? ''),
        'telefono'       => trim($_POST['telefono']       ?? ''),
        'direccion'      => trim($_POST['direccion']      ?? ''),
        'activa'         => isset($_POST['activa']) ? 1 : 0,
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && es_admin()) {
    if (isset($_POST['crear_com'])) {
        $d = _com_from_post();
        if ($d['nombre']) {
            q_comercializadora_crear($pdo, $d);
            header("Location: ?action=comercializadoras&msg=" . urlencode('Comercializadora creada correctamente.'));
            exit;
        }
    }

    if (isset($_POST['editar_com'])) {
        $id = (int)$_POST['com_id'];
        $d  = _com_from_post();
        if ($id && $d['nombre']) {
            q_comercializadora_editar($pdo, $id, $d);
            header("Location: ?action=comercializadoras&msg=" . urlencode('Comercializadora actualizada correctamente.'));
            exit;
        }
    }

    if (isset($_POST['toggle_com'])) {
        q_comercializadora_toggle($pdo, (int)$_POST['com_id'], (int)$_POST['activa']);
        header("Location: ?action=comercializadoras&msg=" . urlencode('Estado actualizado.'));
        exit;
    }

    if (isset($_POST['eliminar_com'])) {
        q_comercializadora_eliminar($pdo, (int)$_POST['com_id']);
        header("Location: ?action=comercializadoras&msg=" . urlencode('Comercializadora eliminada.'));
        exit;
    }
}

$coms     = q_comercializadoras_all($pdo);
$is_admin = es_admin();

/* ── Helper: menú ⋮ ── */
function com_menu_html(array $c): string {
    $json       = htmlspecialchars(json_encode($c), ENT_QUOTES);
    $toggle_val = $c['activa'] ? 0 : 1;
    $toggle_txt = $c['activa'] ? '⏸ Desactivar' : '▶ Activar';

    return <<<HTML
    <div class="oferta-menu-wrap">
        <button class="btn-menu" onclick="abrirMenuOpciones(this,event)" title="Opciones">⋮</button>
        <div class="menu-opciones">
            <button class="menu-opcion" onclick="abrirModalEditarCom({$json},event)">✏ Editar</button>
            <form method="POST">
                <input type="hidden" name="com_id" value="{$c['id']}">
                <input type="hidden" name="activa" value="{$toggle_val}">
                <button type="submit" name="toggle_com" class="menu-opcion">{$toggle_txt}</button>
            </form>
            <form method="POST" onsubmit="return confirm('¿Eliminar esta comercializadora?')">
                <input type="hidden" name="com_id" value="{$c['id']}">
                <button type="submit" name="eliminar_com" class="menu-opcion menu-opcion-eliminar">🗑 Eliminar</button>
            </form>
        </div>
    </div>
HTML;
}

/* ── Helper: card ── */
function com_card_html(array $c, bool $is_admin): string {
    $inactive  = $c['activa'] ? '' : 'inactive';
    $badge_cls = $c['activa'] ? 'badge-contratado' : 'badge-descartado';
    $badge_txt = $c['activa'] ? 'Activa' : 'Inactiva';

    $nombre  = htmlspecialchars($c['nombre']);
    $email   = htmlspecialchars($c['email_contacto'] ?? '');
    $tel     = htmlspecialchars($c['telefono']       ?? '');
    $dir     = htmlspecialchars($c['direccion']      ?? '');
    $menu    = $is_admin ? com_menu_html($c) : '';

    $email_html = $email ? "<div class='com-dato'>✉ {$email}</div>" : '';
    $tel_html   = $tel   ? "<div class='com-dato'>📞 {$tel}</div>"  : '';
    $dir_html   = $dir   ? "<div class='com-dato'>📍 {$dir}</div>"  : '';

    return <<<HTML
<div class="oferta-card {$inactive}">
    <div class="oferta-header">
        <div class="oferta-header-info">
            <div class="oferta-name">{$nombre}</div>
            <div class="com-datos">
                {$email_html}
                {$tel_html}
                {$dir_html}
            </div>
        </div>
        {$menu}
    </div>
    <div class="oferta-footer">
        <span class="badge {$badge_cls}">{$badge_txt}</span>
    </div>
</div>
HTML;
}
?>

<div class="section-header">
    <div class="section-title">Comercializadoras</div>
    <?php if ($is_admin): ?>
    <button class="btn btn-primary" onclick="abrirModalCrearCom()">+ Nueva comercializadora</button>
    <?php endif; ?>
</div>
<div class="section-sub"><?= count($coms) ?> comercializadoras registradas</div>

<div class="grid grid-3">
    <?php if ($coms): ?>
        <?php foreach ($coms as $c): ?>
            <?= com_card_html($c, $is_admin) ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-ofertas">No hay comercializadoras registradas.</p>
    <?php endif; ?>
</div>

<?php if ($is_admin): require __DIR__ . '/../modals/comercializadora-modal.php'; endif; ?>
<script src="/js/ofertas.js"></script>      <!-- ← añade esta línea -->
<script src="/js/comercializadoras.js"></script>