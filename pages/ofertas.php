<link rel="stylesheet" href="/css/ofertas-revision.css">
<link rel="stylesheet" href="/css/loader-jsonviewer.css">

<?php
require_once __DIR__ . '/../queries/ofertas.queries.php';

if (!$pdo) {
    echo "<div class='alert alert-error'>Error de conexión a la base de datos: " . htmlspecialchars($db_error) . "</div>";
    exit;
}

function _oferta_from_post(): array {
    $floats = function($val) { return (float) str_replace(',', '.', $val ?? 0); };

    return [
        'nombre_oferta'         => trim($_POST['nombre_oferta'] ?? ''),
        'comercializadora'      => trim($_POST['comercializadora'] ?? ''),
        'email_comercializadora'=> trim($_POST['email_comercializadora'] ?? ''),
        'precio_energia_p1'  => $floats($_POST['precio_energia_p1']),
        'precio_energia_p2'  => $floats($_POST['precio_energia_p2']),
        'precio_energia_p3'  => $floats($_POST['precio_energia_p3']),
        'precio_energia_p4'  => $floats($_POST['precio_energia_p4']),
        'precio_energia_p5'  => $floats($_POST['precio_energia_p5']),
        'precio_energia_p6'  => $floats($_POST['precio_energia_p6']),
        'precio_potencia_p1' => $floats($_POST['precio_potencia_p1']),
        'precio_potencia_p2' => $floats($_POST['precio_potencia_p2']),
        'precio_potencia_p3' => $floats($_POST['precio_potencia_p3']),
        'precio_potencia_p4' => $floats($_POST['precio_potencia_p4']),
        'precio_potencia_p5' => $floats($_POST['precio_potencia_p5']),
        'precio_potencia_p6' => $floats($_POST['precio_potencia_p6']),
        'fee_minimo'         => $floats($_POST['fee_minimo']),
        'fee_maximo'         => $floats($_POST['fee_maximo']),
        'version_tarifa'     => trim($_POST['version_tarifa'] ?? ''),
        'tipo_tarifa'        => trim($_POST['tipo_tarifa'] ?? ''),
        'activa'             => isset($_POST['activa']) ? 1 : 0,
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && es_admin()) {
    if (isset($_POST['importar_ofertas'])) {
        $ofertas_import = json_decode($_POST['ofertas_json'], true);
        if (is_array($ofertas_import) && !empty($ofertas_import)) {
            foreach ($ofertas_import as $d) {
                $d['activa'] = 1;
                q_oferta_crear($pdo, $d);
            }
            header("Location: ?action=ofertas&msg=" . urlencode('Ofertas importadas correctamente.'));
            exit;
        }
    }

    if (isset($_POST['crear_oferta'])) {
        $d = _oferta_from_post();
        if ($d['nombre_oferta'] && $d['comercializadora']) {
            q_oferta_crear($pdo, $d);
            header("Location: ?action=ofertas&msg=" . urlencode('Oferta creada correctamente.'));
            exit;
        }
    }

    if (isset($_POST['editar_oferta'])) {
        $id = (int)$_POST['oferta_id'];
        $d  = _oferta_from_post();
        if ($id && $d['nombre_oferta'] && $d['comercializadora']) {
            q_oferta_editar($pdo, $id, $d);
            header("Location: ?action=ofertas&msg=" . urlencode('Oferta actualizada correctamente.'));
            exit;
        }
    }

    if (isset($_POST['toggle_oferta'])) {
        q_oferta_toggle($pdo, (int)$_POST['oferta_id'], (int)$_POST['activa']);
        header("Location: ?action=ofertas&msg=" . urlencode('Estado de oferta actualizado.'));
        exit;
    }

    if (isset($_POST['eliminar_oferta'])) {
        q_oferta_eliminar($pdo, (int)$_POST['oferta_id']);
        header("Location: ?action=ofertas&msg=" . urlencode('Oferta eliminada correctamente.'));
        exit;
    }
}

$ofertas  = q_ofertas_all($pdo);
$is_admin = es_admin();

/* ── Helper: generar bloque de precios ── */
function oferta_prices_html(array $o): string {
    $items = [
        ['Energía P1',  $o['precio_energia_p1']  ?? 0, '€/kWh',    'var(--cyan)'],
        ['Energía P2',  $o['precio_energia_p2']  ?? 0, '€/kWh',    'var(--cyan)'],
        ['Energía P3',  $o['precio_energia_p3']  ?? 0, '€/kWh',    'var(--cyan)'],
        ['Energía P4',  $o['precio_energia_p4']  ?? 0, '€/kWh',    'var(--cyan)'],
        ['Energía P5',  $o['precio_energia_p5']  ?? 0, '€/kWh',    'var(--cyan)'],
        ['Energía P6',  $o['precio_energia_p6']  ?? 0, '€/kWh',    'var(--cyan)'],
        ['Potencia P1', $o['precio_potencia_p1'] ?? 0, '€/kW·día', 'var(--cyan)'],
        ['Potencia P2', $o['precio_potencia_p2'] ?? 0, '€/kW·día', 'var(--cyan)'],
        ['Potencia P3', $o['precio_potencia_p3'] ?? 0, '€/kW·día', 'var(--cyan)'],
        ['Potencia P4', $o['precio_potencia_p4'] ?? 0, '€/kW·día', 'var(--cyan)'],
        ['Potencia P5', $o['precio_potencia_p5'] ?? 0, '€/kW·día', 'var(--cyan)'],
        ['Potencia P6', $o['precio_potencia_p6'] ?? 0, '€/kW·día', 'var(--cyan)'],
        ['Fee máximo',  $o['fee_maximo']         ?? 0, '',         'var(--amber)'],
        ['Fee mínimo',  $o['fee_minimo']         ?? 0, '',         'var(--amber)'],
    ];

    $html = '<div class="oferta-prices">';
    foreach ($items as [$label, $val, $unidad, $color]) {
        if (!$val) continue;
        $html .= '<div>'
               . '<div class="price-label">' . htmlspecialchars($label) . '</div>'
               . '<div class="price-val" style="color:' . $color . '">'
               . htmlspecialchars(number_format($val, 5, ',', '.'))
               . ($unidad ? ' ' . $unidad : '')
               . '</div></div>';
    }
    return $html . '</div>';
}

/* ── Helper: menú ⋮ ── */
function oferta_menu_html(array $o): string {
    $json       = htmlspecialchars(json_encode($o), ENT_QUOTES);
    $toggle_val = $o['activa'] ? 0 : 1;
    $toggle_txt = $o['activa'] ? '⏸ Desactivar' : '▶ Activar';

    return <<<HTML
    <div class="oferta-menu-wrap">
        <button class="btn-menu" onclick="abrirMenuOpciones(this,event)" title="Opciones">⋮</button>
        <div class="menu-opciones">
            <button class="menu-opcion" onclick="abrirModalEditar({$json},event)">✏ Editar</button>
            <form method="POST">
                <input type="hidden" name="oferta_id" value="{$o['id']}">
                <input type="hidden" name="activa" value="{$toggle_val}">
                <button type="submit" name="toggle_oferta" class="menu-opcion">{$toggle_txt}</button>
            </form>
            <form method="POST" onsubmit="return confirm('¿Eliminar esta oferta? No se puede deshacer.')">
                <input type="hidden" name="oferta_id" value="{$o['id']}">
                <button type="submit" name="eliminar_oferta" class="menu-opcion menu-opcion-eliminar">🗑 Eliminar</button>
            </form>
        </div>
    </div>
HTML;
}

/* ── Helper: card completo ── */
function oferta_card_html(array $o, bool $is_admin): string {
    $inactive  = $o['activa'] ? '' : 'inactive';
    $badge_cls = $o['activa'] ? 'badge-contratado' : 'badge-descartado';
    $badge_txt = $o['activa'] ? 'Activa' : 'Inactiva';

    $nombre     = htmlspecialchars($o['nombre_oferta']);
    $comercial  = htmlspecialchars($o['comercializadora']);
    $email_span = $o['email_comercializadora']
        ? '<span class="oferta-email"> · ' . htmlspecialchars($o['email_comercializadora']) . '</span>'
        : '';

    $menu = $is_admin ? oferta_menu_html($o) : '';
    $prices_html = oferta_prices_html($o);

    $extra_fields = [
        'Versión' => $o['version_tarifa'] ?? null,
        'Tipo'    => $o['tipo_tarifa']    ?? null,
    ];
    $extra_html = '';
    foreach ($extra_fields as $label => $val) {
        if (!$val) continue;
        $extra_html .= '<div class="extra-field">' . htmlspecialchars($label) . ': ' . htmlspecialchars($val) . '</div>';
    }

    return <<<HTML
<div class="oferta-card {$inactive}">
    <div class="oferta-header">
        <div class="oferta-header-info">
            <div class="oferta-name">{$nombre}</div>
            <div class="oferta-comercializadora">{$comercial}{$email_span}</div>
            {$extra_html}
        </div>
        {$menu}
    </div>
    {$prices_html}
    <div class="oferta-footer">
        <span class="badge {$badge_cls}">{$badge_txt}</span>
    </div>
</div>
HTML;
}
?>


<!-- Título y botón nueva oferta -->
<!-- ✅ REEMPLAZA el section-header por esto: -->
<div class="section-header">
    <div class="section-title">Gestión de ofertas</div>
    <?php if ($is_admin): ?>
    <div style="display:flex;align-items:center;gap:10px">

        <!-- Ojito: solo visible tras cargar PDF -->
        <button id="btn-ver-tabla"
                onclick="mostrarRevision(ofertasImportadas)"
                title="Ver ofertas extraídas"
                style="display:none;background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--cyan)">
            👁
        </button>

        <!-- Input PDF oculto -->
        <input type="file" id="input-pdf" accept=".pdf"
               style="display:none" onchange="procesarPdf()">

        <!-- Engranaje -->
        <div class="oferta-menu-wrap">
            <button class="btn-menu"
                    onclick="abrirMenuOpciones(this,event)"
                    title="Opciones">⚙</button>
            <div class="menu-opciones menu-opciones--right">
                <label class="menu-opcion" for="input-pdf"
                       onclick="cerrarMenus()">
                    📄 Importar PDF
                </label>
                <button class="menu-opcion"
                        onclick="abrirModalCrear();cerrarMenus()">
                    + Nueva oferta
                </button>
            </div>
        </div>

    </div>
    <?php endif; ?>
</div>
<div class="section-sub"><?= count($ofertas) ?> ofertas en el catálogo</div>

<div class="grid grid-3">
    <?php if ($ofertas): ?>
        <?php foreach ($ofertas as $o): ?>
            <?= oferta_card_html($o, $is_admin) ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-ofertas">No hay ofertas en el catálogo.</p>
    <?php endif; ?>
</div>

<?php if ($is_admin): require __DIR__ . '/../modals/oferta-modal.php'; endif; ?>
<script>
    const WEBHOOK_PDF = <?= json_encode($_ENV['N8N_WEBHOOK_PDF'] ?? '') ?>;
</script>
<script src="/js/ofertas.js"></script>
<script src="/js/ofertas-revision.js"></script>